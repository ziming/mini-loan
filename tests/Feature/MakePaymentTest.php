<?php

namespace Tests\Feature;

use App\LoanContract;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MakePaymentTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_make_a_payment_for_a_approved_loan_contract()
    {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $loanContract = factory(LoanContract::class)->states('approved')->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/payments", [
            'type' => 'cash',
            'amount' => 24.58
        ]);


        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'loan_contract_id' => $loanContract->id,
            'amount' => 24.58,
            'type' => 'cash'
        ]);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'status' => 'approved',
            'amount_paid' => 24.58
        ]);

    }

    public function test_user_cannot_make_payment_to_a_approved_loan_contract_that_is_closed() {

        $user = factory(User::class)->create();

        $loanContract = factory(LoanContract::class)->states('closed')->create([
            'user_id' => $user->id,
            'amount_to_pay' => 88.88,
            'amount_paid' => 88.88,
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/payments", [
            'type' => 'cash',
            'amount' => 88
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('payments', [
            'amount' => 88,
        ]);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'amount_to_pay' => 88.88,
            'amount_paid' => 88.88
        ]);

    }

    public function test_user_cannot_make_an_over_payment_for_an_approved_loan_contract() {
        $user = factory(User::class)->create();

        $loanContract = factory(LoanContract::class)->states('approved')->create([
            'user_id' => $user->id,
            'amount_to_pay' => 88.88,
            'amount_paid' => 0,
        ]);

        Passport::actingAs($user);

//        $this->withoutExceptionHandling();

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/payments", [
            'type' => 'cash',
            'amount' => 123.45
        ]);


        $response->assertStatus(422);

        $this->assertDatabaseMissing('payments', [
            'loan_contract_id' => $loanContract->id,
            'amount' => 123.45,
            'type' => 'cash'
        ]);

        $this->assertEquals($loanContract->fresh()->amount_paid, 0);
    }


    public function test_user_cannot_make_payment_if_loan_contract_is_not_hers() {

        $user = factory(User::class)->create();

        $loanContract = factory(LoanContract::class)->states('closed')->create([
            'amount_to_pay' => 88.88,
            'amount_paid' => 22.22,
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/payments", [
            'type' => 'cash',
            'amount' => 30
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('payments', [
            'type' => 'cash',
            'amount' => 30,
        ]);

        $this->assertDatabaseHas('loan_contracts', [
            'id' => $loanContract->id,
            'amount_to_pay' => 88.88,
            'amount_paid' => 22.22
        ]);
    }
}
