<?php

declare(strict_types=1);

namespace App\Service;

use Illuminate\Support\Facades\Http;

class AuthorizePayment
{
    /** @var string */
    private $serviceUrl = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";

    /**
     * @return mixed
     */
    public function execute()
    {
        $response = Http::get($this->serviceUrl);
        return json_decode($response->body());
    }
}
