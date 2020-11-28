<?php

namespace App\Jobs;

use App\Models\Transaction;

class NotifyJob extends Job
{
    /**
     * @var \App\Models\Transaction
     */
    private $transaction;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
