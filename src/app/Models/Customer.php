<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method mixed paginate(int $a)
 * @method mixed find($a)
 * @method create($a)
 * @method findOrFail($a)
 */
class Customer extends Model
{
    use HasFactory;

    public const TYPE_CLIENT = 1;
    public const TYPE_STORE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name', 'email', 'type', 'document', 'balance', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return bool
     */
    public function isStore(): bool
    {
        return $this->getAttribute("type") == self::TYPE_STORE;
    }
}
