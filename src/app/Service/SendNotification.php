<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Customer;
use App\Repository\Transaction;
use Illuminate\Support\Facades\Http;

class SendNotification
{
    public const MESSAGE_SEND = "Enviado";

    /** @var string */
    private $serviceUrl = "https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04";

    /**
     * @param \App\Models\Transaction $transaction
     * @return mixed
     */
    public function execute(\App\Models\Transaction $transaction)
    {
        // In real case, we need send payer data
        $transactionId = $transaction->getAttribute("id");
        $response = Http::get($this->serviceUrl . "?transactionId=" . $transactionId);
        return json_decode($response->body());
    }
}
