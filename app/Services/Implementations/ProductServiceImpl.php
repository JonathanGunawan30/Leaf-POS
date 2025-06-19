<?php

namespace App\Services\Implementations;

use App\Events\StockAlertEvent;
use App\Mail\StockAlertMail;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Interfaces\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class ProductServiceImpl implements ProductService
{
    private function generateSku(string $productName, string $categoryName): string
    {
        $cat = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $categoryName), 0, 3));
        $prod = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $productName), 0, 3));
        $code = strtoupper(Str::random(4));

        $sku = "{$cat}-{$prod}-{$code}";

        while (Product::where('sku', $sku)->exists()) {
            $code = strtoupper(Str::random(4));
            $sku = "{$cat}-{$prod}-{$code}";
        }

        return $sku;
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = str_pad(mt_rand(100000000000, 999999999999), 13, '0', STR_PAD_LEFT);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['images'])) {
                $originalExtension = $data['images']->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $originalExtension;
                $path = $data['images']->storeAs('product-images', $filename, 'public');
                $data['images'] = $path;
            } else {
                $data['images'] = 'product-images/product-default.jpg';
            }

            if (empty($data['sku'])) {
                $category = Category::find($data['category_id']);
                $categoryName = $category?->name ?? 'PRD';
                $data['sku'] = $this->generateSku($data['name'], $categoryName);
            }

            if (empty($data['barcode'])) {
                $data['barcode'] = $this->generateUniqueBarcode();
            }

            $taxes = $data['taxes'] ?? [];
            unset($data['taxes']);
            $product = Product::create($data);

            foreach ($taxes as $tax) {
                $taxModel = Tax::find($tax['tax_id']);

                if (!$taxModel) {
                    continue;
                }
                $amount = ($taxModel->rate / 100) * $data['purchase_price'];
                $product->taxes()->attach($taxModel->id, ['amount' => $amount]);
            }


            return $product;
        });
    }
    public function showProduct($id): Product
    {
        return Product::withTrashed()->with(['unit', 'category', 'taxes'])->findOrFail($id);
    }

    public function generateBarcodeBase64(string $barcode): string
{
    $generator = new DNS1D();
    $generator->setStorPath(storage_path('framework/barcodes'));

    return $generator->getBarcodePNG($barcode, 'C128');
}

    public function update(array $data, $id): Product
    {
        return DB::transaction(function () use ($data, $id) {
            $product = Product::with('taxes')->findOrFail($id);
            $oldPurchasePrice = $product->purchase_price;

            if (isset($data['remove_image']) && $data['remove_image'] === '1') {
                $data['images'] = 'product-images/product-default.jpg';
            }

            if (isset($data['images']) && is_object($data['images'])) {
                $originalExtension = $data['images']->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $originalExtension;
                $path = $data['images']->storeAs('product-images', $filename, 'public');
                $data['images'] = $path;
            }

            $product->update($data);

            if (array_key_exists('taxes', $data)) {
                $this->syncProductTaxes($product, $data['taxes']);
            } elseif (isset($data['purchase_price']) && $data['purchase_price'] != $oldPurchasePrice) {
                $this->recalculateProductTaxAmounts($product, $data['purchase_price']);
            }

            if ($product->stock <= $product->stock_alert) {
                // Create notification service instance
                $notificationService = new NotificationService();

                // Create stock alert notification
                $notification = $notificationService->createStockAlert($product, null, false);

                // Broadcast the event with the notification
                event(new StockAlertEvent($product, $notification));

                $users = User::whereHas('role', function ($query) {
                    $query->whereIn('name', ['Admin', 'Purchasing', 'Inventory']);
                })->get();

                foreach ($users as $user) {
                    Mail::to($user->email)->send(new StockAlertMail($product));
                }
            }


            return $product->refresh()->load(['unit', 'category', 'taxes']);
        });
    }


    private function syncProductTaxes(Product $product, array $taxIds): void
    {
        $purchasePrice = $product->purchase_price;

        $taxesToSync = [];

        foreach ($taxIds as $taxId) {
            $tax = Tax::findOrFail($taxId);
            $amount = $purchasePrice * ($tax->rate / 100);
            $taxesToSync[$taxId] = ['amount' => $amount];
        }

        $product->taxes()->sync($taxesToSync);
    }

    private function recalculateProductTaxAmounts(Product $product, int|float $newPurchasePrice): void
    {
        foreach ($product->taxes as $tax) {
            $amount = $newPurchasePrice * ($tax->rate / 100);
            $product->taxes()->updateExistingPivot($tax->id, ['amount' => $amount]);
        }
    }

    public function getAll()
    {
        $query = Product::with(['unit', 'category', 'taxes'])->latest();

        if ($search = request()->get('search')) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%")
            ->orWhere('barcode', 'like', "%{$search}%");
        }

        if ($categoryId = request()->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($minPurchase = request()->get('min_purchase')) {
            $query->where('purchase_price', '>=', $minPurchase);
        }

        if ($maxPurchase = request()->get('max_purchase')) {
            $query->where('purchase_price', '<=', $maxPurchase);
        }

        if ($minSelling = request()->get('min_selling')) {
            $query->where('selling_price', '>=', $minSelling);
        }

        if ($maxSelling = request()->get('max_selling')) {
            $query->where('selling_price', '<=', $maxSelling);
        }

        $perPage = request()->get('per_page', 10);
        $products =  $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->barcode_image = $this->generateBarcodeBase64($product->barcode);
            return $product;
        });

        return $products;
    }

    public function softdelete($id)
    {
        $product = Product::findOrFail($id);

        if($product->purchases()->withTrashed()->exists()){
            throw new \Exception("Cannot delete because it is still associated with one or more purchases.", 400);
        }
        $product->delete();
        return $product;
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if(!$product->trashed()){
            throw new \Exception("Cannot restore, product is not deleted", 400);
        }

        $product->restore();
        return $product;
    }

    public function harddelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        if(!$product->trashed()){
            throw new \Exception("Cannot hard delete, product is not soft deleted", 400);
        }
        if($product->purchases()->withTrashed()->exists()){
            throw new \Exception("Cannot delete because it is still associated with one or more purchases.", 400);
        }

        $product->taxes()->detach();
        $product->forceDelete();
        return $product;
    }

    public function trashed()
    {
        $query = Product::onlyTrashed()
            ->with(['unit', 'category', 'taxes'])
            ->latest('deleted_at');

        if ($search = request()->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = request()->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($minPurchase = request()->get('min_purchase')) {
            $query->where('purchase_price', '>=', $minPurchase);
        }

        if ($maxPurchase = request()->get('max_purchase')) {
            $query->where('purchase_price', '<=', $maxPurchase);
        }

        if ($minSelling = request()->get('min_selling')) {
            $query->where('selling_price', '>=', $minSelling);
        }

        if ($maxSelling = request()->get('max_selling')) {
            $query->where('selling_price', '<=', $maxSelling);
        }

        $perPage = request()->get('per_page', 10);
        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->barcode_image = $this->generateBarcodeBase64($product->barcode);
            return $product;
        });

        return $products;
    }


}
