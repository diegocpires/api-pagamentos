<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;

class FraudDetection
{
    /** @var string */
    private $serviceUrl = "https://run.mocky.io/v3/8088c898-89bb-47b5-a63a-694e1b97d1b8";

    /**
     * @param \App\Models\Customer $payer
     * @return mixed
     */
    public function execute(Customer $payer)
    {
        // In real case, we need send payer data
        $payerId = $payer->getAttribute("id");
        $response = Http::get($this->serviceUrl . "?payerId=" . $payerId);
        return json_decode($response->body());
    }
}
