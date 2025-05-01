<?php

namespace App\Observers;

use App\Models\Courier;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class ShipmentObserver
{
    public function created(Shipment $shipment)
    {
        DB::transaction(function () use ($shipment) {
            $shipment->courier()->update(['status' => 'unavailable']);
        });
    }

    public function updated(Shipment $shipment)
    {
        DB::transaction(function () use ($shipment) {
            // Cek jika status berubah menjadi delivered/cancelled
            if ($this->shouldUpdateCourierStatus($shipment)) {
                $this->updateCourierAvailability($shipment->courier);
            }
            // Cek jika courier berubah
            elseif ($shipment->wasChanged('courier_id')) {
                // Update status courier baru
                $hasCourierActiveShipments = Shipment::where('courier_id', $shipment->courier_id)
                    ->whereNotIn('status', ['delivered', 'cancelled'])
                    ->exists();

                if ($hasCourierActiveShipments) {
                    $shipment->courier()->update(['status' => 'unavailable']);
                }

                // Update status courier lama
                if ($shipment->getOriginal('courier_id')) {
                    $oldCourier = Courier::find($shipment->getOriginal('courier_id'));
                    if ($oldCourier) {
                        $this->updateCourierAvailability($oldCourier);
                    }
                }
            }
        });
    }

    public function deleted(Shipment $shipment)
    {
        $this->updateCourierAvailability($shipment->courier);
    }

    public function forceDeleted(Shipment $shipment)
    {
        $this->updateCourierAvailability($shipment->courier);
    }

    protected function shouldUpdateCourierStatus(Shipment $shipment): bool
    {
        $relevantStatuses = ['delivered', 'cancelled'];
        return $shipment->wasChanged('status') &&
            in_array($shipment->status, $relevantStatuses);
    }

    protected function updateCourierAvailability(Courier $courier): void
    {
        // Refresh courier untuk mendapatkan data terbaru
        $courier->refresh();

        $hasActiveShipments = Shipment::where('courier_id', $courier->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->exists();

        if (!$hasActiveShipments && $courier->status !== 'available') {
            $courier->update(['status' => 'available']);
        } else if ($hasActiveShipments && $courier->status !== 'unavailable') {
            $courier->update(['status' => 'unavailable']);
        }
    }

}
