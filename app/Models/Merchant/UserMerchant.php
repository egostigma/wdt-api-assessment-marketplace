<?php

namespace App\Models\Merchant;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMerchant extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(UserMerchantProduct::class);
    }

    public function getCreatedAtFormattedAttribute()
    {
        if ($this->created_at) {
            return $this->created_at->translatedFormat("l, d F Y, H:i");
        }
    }
}
