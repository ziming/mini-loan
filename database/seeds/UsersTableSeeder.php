<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class, 8)
            ->create()
            ->each(function (\App\User $user) {
                $user->loanContracts()->saveMany(factory(App\LoanContract::class, 8)->make());
            });

    }
}
