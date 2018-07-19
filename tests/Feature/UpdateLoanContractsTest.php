<?php

namespace Tests\Feature;

use App\LoanContract;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateLoanContractsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_her_own_draft_loan_contracts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->states('draft')->create([
           'user_id' => $user->id,
        ]);

        $response = $this->json('patch', "/api/loan-contracts/{$loanContract->id}", [
            'borrowed_amount' => 5000,
            'days_proposed' => 168,
            'repayment_frequency' => 'yearly'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'borrowed_amount' => 5000,
            'days_proposed' => 168,
            'repayment_frequency' => 'yearly'
        ]);
    }

    public function test_user_cannot_update_someone_else_draft_loan_contracts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->create();

        $response = $this->json('put', "/api/loan-contracts/{$loanContract->id}", [
            'borrowed_amount' => 5000,
            'days_proposed' => 168,
            'repayment_frequency' => 'yearly'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'borrowed_amount' => $loanContract->borrowed_amount,
            'days_proposed' => $loanContract->days_proposed,
            'repayment_frequency' => $loanContract->repayment_frequency
        ]);
    }

    public function test_user_cannot_update_own_non_draft_loan_contracts()
    {

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->states('pending')->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('patch', "/api/loan-contracts/{$loanContract->id}", [
            'borrowed_amount' => 5000,
            'days_proposed' => 168,
            'repayment_frequency' => 'yearly'
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'borrowed_amount' => $loanContract->borrowed_amount,
            'days_proposed' => $loanContract->days_proposed,
            'repayment_frequency' => $loanContract->repayment_frequency
        ]);
    }
}
