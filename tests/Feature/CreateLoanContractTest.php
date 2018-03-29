<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLoanContractTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_create_loan_contract()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

//        $this->withoutExceptionHandling();

        $response = $this->json('post', "/api/users/{$user->id}/loan-contracts", [
            'borrowed_amount' => 5001,
            'days_proposed' => 168,
            'repayment_frequency' => 'weekly',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Loan Contract Created'
        ]);

        $this->assertDatabaseHas('loan_contracts', [
            'user_id' => $user->id,
            'borrowed_amount' => 5001,
            'days_proposed' => 168,
            'repayment_frequency' => 'weekly',
        ]);

    }
}
