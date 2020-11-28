<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @var \App\Models\Customer
     */
    private $customer;
    /**
     * @var \App\Repository\Transaction
     */
    private $transaction;

    public function __construct(
        Customer $customer,
        \App\Repository\Transaction $transaction
    ) {
        $this->customer = $customer;
        $this->transaction = $transaction;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, [
            'payer' => 'required|exists:customers,id',
            'payee' => 'required|exists:customers,id',
            'value' => 'required|numeric'
        ]);

        $payload = $request->all();
        $payer = $this->customer->find($payload["payer"]);
        $payee = $this->customer->find($payload["payee"]);

        $this->transaction->validate($payer, $payee);
        $this->transaction->execute($payer, $payee, $payload["value"]);

        //@phpstan-ignore-next-line
        return response()->json(["data" => ["message" => "Transaction done!"]], 200);
    }
}
