<?php

namespace App\Models\Cart;

use App\Models\Merchant\UserMerchantProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCart extends Model
{
    use SoftDeletes;

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(UserMerchantProduct::class);
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
