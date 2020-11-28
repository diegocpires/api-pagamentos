<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exceptions\BadTransactionException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\OpenTransactionException;
use App\Exceptions\PossibleFraudException;
use App\Jobs\NotifyJob;
use App\Models\Customer;
use App\Service\AuthorizePayment;
use App\Service\Cashback;
use App\Service\FraudDetection;
use App\Service\SendNotification;
use Exception;

class Transaction
{
    /**
     * @var \App\Service\FraudDetection
     */
    private $fraudService;
    /**
     * @var \App\Service\AuthorizePayment
     */
    private $authorizeService;
    /**
     * @var \App\Service\Cashback
     */
    private $cashbackService;
    /**
     * @var \App\Service\SendNotification
     */
    private $notificationService;
    /**
     * @var \App\Models\Transaction
     */
    private $transactionModel;

    public function __construct(
        FraudDetection $fraudDetection,
        AuthorizePayment $authorizePayment,
        Cashback $cashback,
        SendNotification $sendNotification,
        \App\Models\Transaction $transaction
    ) {
        $this->fraudService = $fraudDetection;
        $this->authorizeService = $authorizePayment;
        $this->cashbackService = $cashback;
        $this->notificationService = $sendNotification;
        $this->transactionModel = $transaction;
    }

    /**
     * @param \App\Models\Customer $payer
     * @param \App\Models\Customer $payee
     * @return bool
     * @throws \Exception
     */
    public function validate(Customer $payer, Customer $payee): bool
    {
        if ($payer->isStore()) {
            throw new Exception("Payer must not be a Store");
        }

        $this->verifyOpenTransactions($payer->getAttribute("id"), $payee->getAttribute("id"));
        $this->checkFraud($payer);
        $this->checkFunds();

        return true;
    }

    /**
     * @param \App\Models\Customer $payer
     * @param \App\Models\Customer $payee
     * @param float                $value
     * @throws \App\Exceptions\BadTransactionException
     * @throws \Throwable
     */
    public function execute(Customer $payer, Customer $payee, float $value): void
    {
        $transaction = $this->transactionModel;

        $transaction->setAttribute("payer", $payer->getAttribute("id"));
        $transaction->setAttribute("payee", $payee->getAttribute("id"));
        $transaction->setAttribute("value", $value);
        $transaction->setAttribute("status", \App\Models\Transaction::STATUS_OPEN);
        $transaction->saveOrFail();

        $cashbackResult = $this->checkCashback();

        $transaction->setAttribute("status", \App\Models\Transaction::STATUS_CLOSE);
        $payer->setAttribute("balance", $payer->getAttribute("balance") - $value + $cashbackResult);
        $payee->setAttribute("balance", $payee->getAttribute("balance") + $value);
        try {
            $payer->saveOrFail();
            $payee->saveOrFail();
            $transaction->saveOrFail();
            $this->sendNotification($transaction);
        } catch (\Throwable $exception) {
            $transaction->setAttribute("status", \App\Models\Transaction::STATUS_REFUND);
            $transaction->saveOrFail();
            throw new BadTransactionException("Transaction not completed", 400, $exception);
        }
    }

    /**
     * @param int|null $payer
     * @param int|null $payee
     * @throws \App\Exceptions\OpenTransactionException
     */
    private function verifyOpenTransactions(?int $payer, ?int $payee): void
    {
        $result =  $this->transactionModel->verifyOpenTransaction($payer, $payee);
        if (isset($result)) {
            throw new OpenTransactionException("Payer or Payee already has an open transaction", 400);
        }
    }

    /**
     * @param Customer $payer
     * @return void
     * @throws \Exception
     */
    private function checkFraud(Customer $payer): void
    {
        $fraudResult = $this->fraudService->execute($payer);
        if ($fraudResult->score < \App\Models\Transaction::SCORE_FRAUD) {
            throw new PossibleFraudException("Possible Fraud Detected", 400);
        }
    }

    /**
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    private function sendNotification(\App\Models\Transaction $transaction): void
    {
        $sendResult = $this->notificationService->execute($transaction);
        if ($sendResult->message != SendNotification::MESSAGE_SEND) {
            dispatch(new NotifyJob($transaction))->onQueue('notify');
        }
    }

    /**
     * @throws \Exception
     */
    private function checkFunds(): void
    {
        $fundsResult = $this->authorizeService->execute();
        if ($fundsResult->message !== \App\Models\Transaction::AUTHORIZE_MESSAGE) {
            throw new InsufficientFundsException("Payer does not have funds", 400);
        }
    }

    /**
     * @return float
     */
    private function checkCashback(): float
    {
        $cashbackResult = $this->cashbackService->execute();
        if ($cashbackResult->is_eligible) {
            return $cashbackResult->cashback_value;
        }
        return 0;
    }
}
