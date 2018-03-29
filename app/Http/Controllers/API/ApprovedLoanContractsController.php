<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\LoanContract;
use Illuminate\Http\Request;

class ApprovedLoanContractsController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
//        $this->middleware(['auth:api', 'scopes:approve-loan-contract, un-approve-loan-contract');
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

        $this->authorize('approve', $loanContract);

        $loanContract->update(['status' => 'approved']);


    }


}
