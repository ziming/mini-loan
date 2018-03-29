<?php

use Faker\Generator as Faker;

$factory->define(App\LoanContract::class, function (Faker $faker) {

    $borrowedAmount = $faker->numberBetween(5000, 50000);
    $interestRate = $faker->randomElement([5.9, 11.5]);

    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },

        'borrowed_amount' => $borrowedAmount,
        'days_proposed' => $faker->randomElement([7 * 12, 7 * 24]),
        'repayment_frequency' => $faker->randomElement(['weekly', 'monthly', 'yearly']),
        'interest_rate' => $interestRate,
        'status' => $faker->randomElement(['draft', 'pending', 'approved', 'closed']),

        'amount_to_pay' => $borrowedAmount * (1 + ($interestRate / 100)),
    ];

});

$factory->state(App\LoanContract::class, 'draft', function (Faker $faker) {
    return [
        'status' => 'draft',
    ];
});

$factory->state(App\LoanContract::class, 'pending', function (Faker $faker) {
    return [
        'status' => 'pending',
    ];
});

$factory->state(App\LoanContract::class, 'approved', function (Faker $faker) {
    return [
        'status' => 'approved',
    ];
});


$factory->state(App\LoanContract::class, 'closed', function (Faker $faker) {
    return [
        'status' => 'closed',
    ];
});