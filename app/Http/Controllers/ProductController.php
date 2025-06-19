<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Interfaces\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(CreateProductRequest $request)
    {
        try {
            $data = $request->validated();
            $product = $this->productService->create($data);
            return response()->json([
                "data" => new ProductResource($product->load(['unit', 'category', 'taxes'])),
                "message" => "Product created successfully",
                "statusCode" => 201
            ], 201);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productService->showProduct($id);
            $barcodeBase64 = $this->productService->generateBarcodeBase64($product->barcode);

            $product->barcode_image = $barcodeBase64;

            return (new ProductResource($product))->additional([
                'message' => 'Product retrieved successfully',
                'statusCode' => 200,
            ]);


        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
      try {
          $data = $request->validated();
          $product = $this->productService->update($data, $id);

          return response()->json([
              "data" => new ProductResource($product->load(['unit', 'category', 'taxes'])),
              "message" => "Product updated successfully",
              "statusCode" => 200
          ]);
      }catch(ModelNotFoundException $e){
          return response()->json([
              "errors" => [
                  "message" => "Product not found"
              ]
          ], 404);
      } catch (\Throwable $e ){
          return response()->json([
              "errors" => [
                  "message" => "Something went wrong "
              ]
          ], 500);
      }
    }

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $request->get('per_page', 10);
            $products = $this->productService->getAll();
            return (ProductResource::collection($products))->additional([
                "message" => "Product retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong",
                ]
            ], 500);
        }
    }

    public function delete($id): JsonResponse
    {
        try {
            $this->productService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Product deleted successfully"
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
        catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function restore($id): ProductResource|JsonResponse
    {
        try {
            $product = $this->productService->restore($id);
            return (new ProductResource($product->load(['unit', 'category', 'taxes'])))->additional([
                "message" => "Product restored successfully",
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function force($id): JsonResponse
    {
        try {
            $this->productService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Product deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function trashed(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $products = $this->productService->trashed();

            return ProductResource::collection($products)->additional([
                "message" => "Product retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

}
