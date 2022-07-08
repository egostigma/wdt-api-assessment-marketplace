<?php

namespace App\Models\Order;

use App\Models\Merchant\UserMerchantProduct;
use Illuminate\Database\Eloquent\Model;

class UserOrderProduct extends Model
{
    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
    ];

    public function detail()
    {
        return $this->belongsTo(UserMerchantProduct::class, "product_id");
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
