<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserVerification extends Model
{
    protected $fillable = ['user_id', 'token', 'expired_at'];

    public function isExpired()
    {
        return $this->expired_at && $this->expired_at->lt(Carbon::now()) ? true : false; 
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
