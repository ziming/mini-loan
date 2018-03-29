<?php

namespace Tests\Feature;

use App\LoanContract;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApproveLoanContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_loan_contract()
    {
        $admin = factory(User::class)->create([
            'is_admin' => true,
        ]);

        $loanContract = factory(LoanContract::class)->states('pending')->create();

        $this->actingAs($admin);

        Passport::actingAs($admin);

        // https://github.com/adamwathan/laracon2017/pull/4
        // I decide to follow DanielDarrenJones suggestion on the page instead of Adam's one for this

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/approve");

        $this->assertEquals('approved', $loanContract->fresh()->status);
    }

    public function test_non_admin_cannot_approve_loan_contract()
    {
        $user = factory(User::class)->create([
            'is_admin' => false,
        ]);

        $loanContract = factory(LoanContract::class)->states('pending')->create();

        Passport::actingAs($user);

        // https://github.com/adamwathan/laracon2017/pull/4
        // I decide to follow DanielDarrenJones suggestion on the page instead of Adam's one for this

        $response = $this->json('post', "/api/loan-contracts/{$loanContract->id}/approve");

        $this->assertEquals('pending', $loanContract->fresh()->status);
    }


}
