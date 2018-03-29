<?php

namespace Tests\Feature;

use App\LoanContract;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteLoanContractsTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_user_can_delete_her_own_draft_loan_contract()
    {
        $user = factory(User::class)->create();

        $loanContract = factory(LoanContract::class)->states('draft')->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id
        ]);

        $response = $this->json('delete', "/api/loan-contracts/{$loanContract->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('loan_contracts', [
            'id' => $loanContract->id
        ]);


    }

    public function test_user_cannot_delete_another_user_loan_contract() {


        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->states('draft')->create();

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id
        ]);

        $response = $this->json('delete', "/api/loan-contracts/{$loanContract->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id
        ]);
    }

    public function test_user_cannot_delete_her_own_non_draft_loan_contract() {


        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $loanContract = factory(LoanContract::class)->states($this->faker->randomElements(['pending', 'approved']))->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id
        ]);

        // Try deleting the pending loan contract
        $response = $this->json('delete', "/api/loan-contracts/{$loanContract->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id
        ]);


    }
}
