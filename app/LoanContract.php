<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanContract extends Model
{

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'borrowed_amount' => 'integer',
        'days_proposed' => 'integer',
        'interest_rate' => 'double',
        'amount_paid' => 'double',
        'amount_to_pay' => 'double',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
