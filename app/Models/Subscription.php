<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $guarded = []; // This allows all attributes to be mass assignable

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'last_use' => 'datetime', // Automatically cast to Carbon instance
    ];

}
