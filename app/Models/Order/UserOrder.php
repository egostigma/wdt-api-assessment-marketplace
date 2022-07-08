<?php

namespace App\Models\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    const UNCONFIRMED = 0;
    const CONFIRMED = 1;
    const CANCELLED = 2;
    const REJECTED = 3;

    protected $casts = [
        "price" => "float",
        "quantity" => "integer",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(UserOrderProduct::class, "user_order_id");
    }

    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case self::UNCONFIRMED:
                return "UNCONFIRMED";
                break;
            case self::CONFIRMED:
                return "CONFIRMED";
                break;
            case self::CANCELLED:
                return "CANCELLED";
                break;
            case self::REJECTED:
                return "REJECTED";
                break;
        }
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
