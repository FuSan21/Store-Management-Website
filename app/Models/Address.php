<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'label', 'recipient', 'phone', 'line', 'city', 'is_default_shipping', 'is_default_billing'];

    protected function casts(): array
    {
        return [
            'is_default_shipping' => 'boolean',
            'is_default_billing' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The address as it is stored on an order.
     */
    public function oneLine(): string
    {
        return collect([$this->recipient, $this->line, $this->city])->filter()->implode(', ');
    }
}
