<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMerchantProduct extends Model
{
    use SoftDeletes;

    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
    ];

    public function scopeOtherMerchants()
    {
        if (!!auth()->user()) {
            return $this->whereHas("merchant", function ($query) {
                $query->where("user_id", "!=", auth()->user()->id);
            });
        }
    }

    public function merchant()
    {
        return $this->belongsTo(UserMerchant::class, "user_merchant_id");
    }

    public function getCreatedAtFormattedAttribute()
    {
        if ($this->created_at) {
            return $this->created_at->translatedFormat("l, d F Y, H:i");
        }
    }

    public function getUpdatedAtFormattedAttribute()
    {
        if ($this->updated_at) {
            return $this->updated_at->translatedFormat("l, d F Y, H:i");
        }
    }
}
