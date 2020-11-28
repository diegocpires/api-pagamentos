<?php

declare(strict_types=1);

namespace App\Service;

use Illuminate\Support\Facades\Http;

class Cashback
{
    /** @var string */
    private $serviceUrl = "https://run.mocky.io/v3/177c6f18-010e-4c98-bfb5-78d95961c070";

    /**
     * @return mixed
     */
    public function execute()
    {
        $response = Http::get($this->serviceUrl);
        return json_decode($response->body());
    }
}
