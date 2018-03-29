<?php

namespace App\Policies;

use App\User;
use App\LoanContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the loanContract.
     *
     * @param  \App\User  $user
     * @param  \App\LoanContract  $loanContract
     * @return mixed
     */
    public function view(User $user, LoanContract $loanContract)
    {
        return $user->id === $loanContract->user_id;
    }

    /**
     * Determine whether the user can create loanContracts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the loanContract.
     *
     * @param  \App\User  $user
     * @param  \App\LoanContract  $loanContract
     * @return mixed
     */
    public function update(User $user, LoanContract $loanContract)
    {
        return $user->id === $loanContract->user_id && $loanContract->status === 'draft';
    }

    /**
     * Determine whether the user can delete the loanContract.
     *
     * @param  \App\User  $user
     * @param  \App\LoanContract  $loanContract
     * @return mixed
     */
    public function delete(User $user, LoanContract $loanContract)
    {
        return $user->id === $loanContract->user_id && $loanContract->status === 'draft';
    }

    /**
     * Determine whether the user can make payment for a loanContract.
     *
     * @param  \App\User  $user
     * @param  \App\LoanContract  $loanContract
     * @return mixed
     */
    public function makePayment(User $user, LoanContract $loanContract)
    {
        return $user->id === $loanContract->user_id && $loanContract->status === 'approved';
    }

    public function approve(User $user, LoanContract $loanContract)
    {
        return $user->is_admin && $loanContract->status === 'pending';
    }
}
