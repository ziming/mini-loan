<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function loanContract() {
        return $this->belongsTo(LoanContract::class);
    }
}
