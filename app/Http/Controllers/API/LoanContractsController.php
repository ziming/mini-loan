<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\LoanContract;
use Illuminate\Http\Request;

class LoanContractsController extends ApiController
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
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $loanContract = LoanContract::findOrFail($id);

        $this->authorize('view', $loanContract);

        return $loanContract;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'borrowed_amount'     => ['required', 'integer', 'min:5000', 'max:50000'],
            'days_proposed'       => ['required', 'integer', 'min:28', 'max:168'],
            'repayment_frequency' => ['required', 'string', 'in:weekly,monthly,yearly'],
        ]);

        $loanContract = LoanContract::findOrFail($id);

        $this->authorize('update', $loanContract);

        $loanContract->update($validatedData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loanContract = LoanContract::findOrFail($id);

        $this->authorize('delete', $loanContract);

        $loanContract->delete();
    }
}
