<?php

namespace App\Http\Controllers\API;

use Akaunting\Money\Money;
use App\Http\Controllers\Controller;
use App\LoanContract;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanContractPaymentsController extends ApiController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        $loanContract = LoanContract::findOrFail($id);

        $this->authorize('make-payment', $loanContract);

        $maxAmountToPay = number_format($loanContract->amount_to_pay - $loanContract->amount_paid,
            2, '.', '');

//        dump($maxAmountToPay, request('amount'));

        $validatedData = $request->validate([

            // currently I only accept cash, in future maybe stripe and others
            'type'   => ['required', 'string', 'in:cash'],

            // May want min to at least cover Stripe/Paypal.etc minimum transaction fees
            // to validate decimals https://stackoverflow.com/questions/29734046/laravel-validate-decimal-0-99-99
            // probably create a validation rule for it next time

            // A stackover flow post say I need to add numeric but it feels weird
            // https://stackoverflow.com/questions/29734046/laravel-validate-decimal-0-99-99
            'amount' => ['required', 'numeric', "between:0.01,{$maxAmountToPay}"]
        ]);

        // To be safe, chop off. Is it un-necessary?
        $inputAmount = number_format($validatedData['amount'], 2, '.', '');

        DB::transaction(function () use (&$loanContract, &$validatedData) {
            $loanContract->payments()->save(new Payment($validatedData));
            $loanContract->increment('amount_paid', $validatedData['amount']);

            // Grab a fresh instance from db just to be safe? May not be needed
            $loanContract = $loanContract->fresh();

            if ($loanContract->amount_paid >= $loanContract->amount_to_pay) {
                $loanContract->update(['status' => 'closed']);
            }
        });

        return $this->respondCreated('Payment Made!');


    }

}
