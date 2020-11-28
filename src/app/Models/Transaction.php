<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method mixed where($field, $value)
 */
class Transaction extends Model
{
    public const SCORE_FRAUD = 75;

    public const AUTHORIZE_MESSAGE = "Autorizado";

    public const STATUS_OPEN = 1;
    public const STATUS_CLOSE = 2;
    public const STATUS_REFUND = 3;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'value', 'payer', 'payee', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<string>
     */
    protected $hidden = [];

    /**
     * @param int|null $payer
     * @param int|null $payee
     * @return mixed
     */
    public function verifyOpenTransaction(?int $payer, ?int $payee)
    {
        return \App\Models\Transaction::where('status', \App\Models\Transaction::STATUS_OPEN)
            ->where(function ($q) use ($payer, $payee) {
                $q->where('payer', $payer)
                    ->orWhere('payee', $payee);
            })
            ->first();
    }
}
