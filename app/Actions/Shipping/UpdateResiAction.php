<?php

namespace App\Actions\Shipping;

use App\Models\Shipment;

class UpdateResiAction
{
    public function execute(Shipment $shipment, string $ekspedisi, string $noResi): Shipment
    {
        $shipment->update([
            'ekspedisi' => $ekspedisi,
            'no_resi' => $noResi,
        ]);

        return $shipment;
    }
}