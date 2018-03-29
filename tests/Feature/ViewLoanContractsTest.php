<?php

namespace Tests\Feature;

use App\LoanContract;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewLoanContractsTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_view_all_her_loan_contracts()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $loanContracts = factory(LoanContract::class, 2)->create([
           'user_id' => $user->id
        ]);

//        $this->withoutExceptionHandling();

        $response = $this->json('get', "/api/users/{$user->id}/loan-contracts");

//        $response->dump();

        $response->assertStatus(200);

        $response->assertJsonCount(count($loanContracts));

        $response->assertJsonStructure([
            [
                'borrowed_amount',
                'days_proposed',
                'repayment_frequency',
                'interest_rate',
                'user_id',
                'status'
            ]
        ]);
    }

    public function test_user_can_view_1_of_her_loan_contracts() {

        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->create([
            'user_id' => $user->id
        ])->fresh();

        $response = $this->json('get', "/api/loan-contracts/{$loanContract->id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'borrowed_amount',
            'days_proposed',
            'repayment_frequency',
            'interest_rate',
            'user_id',
            'status'
        ]);

        $response->assertJsonFragment([
//            'borrowed_amount' => $loanContract->borrowed_amount,
            'days_proposed' => $loanContract->days_proposed,
            'repayment_frequency' => $loanContract->repayment_frequency,
            'interest_rate' => $loanContract->interest_rate,
            'user_id' => $loanContract->user_id,
            'status' => $loanContract->status
        ]);

    }

    public function test_user_cannot_view_a_loan_contract_that_is_not_hers() {

        $users = factory(User::class, 2)->create();

        Passport::actingAs($users[0]);

        $loanContract = factory(LoanContract::class)->create([
            'user_id' => $users[1]->id
        ])->fresh();

        $response = $this->json('get', "/api/loan-contracts/{$loanContract->id}");

        $response->assertStatus(403);

    }
}
