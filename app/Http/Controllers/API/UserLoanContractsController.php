<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\LoanContract;
use Illuminate\Http\Request;

class UserLoanContractsController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $id)
    {
        // Policy and Gate returns 403 Forbidden if fail
        abort_if($id !== auth()->id(), 403,
            'You are unauthorized to view the loan contracts for this user');

        return LoanContract::where('user_id', $id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $id)
    {

        abort_if($id !== auth()->id(), 403,
            'You are not allowed to create a new loan contract for this user');

        $request->validate([
            'borrowed_amount'     => ['required', 'integer', 'min:5000', 'max:50000'],
            'days_proposed'       => ['required', 'integer', 'min:28', 'max:168'],
            'repayment_frequency' => ['required', 'string', 'in:weekly,monthly,yearly'],
        ]);

        $daysDuration = intval(request('days_proposed'));

        LoanContract::create([
            'user_id'             => $id,
            'borrowed_amount'     => request('borrowed_amount'),
            'days_proposed'       => $daysDuration,
            'repayment_frequency' => request('repayment_frequency'),
            'interest_rate'       => ($daysDuration < 12) ? 5.9 : 11.5,
            'status'              => 'draft'
        ]);

        return $this->respondCreated('Loan Contract Created');

    }
}
