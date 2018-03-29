<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_contracts', function (Blueprint $table) {
            $table->increments('id');

            // enum or string?
            $table->string('status')->default('draft'); // draft, pending, final, paid

            // Proposal Stage
            $table->unsignedInteger('borrowed_amount')->unsigned();
            $table->unsignedInteger('days_proposed')->unsigned(); // in days

            // enum or string?
            $table->string('repayment_frequency')->default('weekly'); // other values 'monthy', 'yearly'

            // max is 99.99% Is in loan contracts in case interest rate changes for future applications
            // we decide the value in this field, not the customer
            $table->unsignedDecimal('interest_rate', 4, 2);


            // Official Loan stage
            $table->date('start_date')->nullable();

            // End date can be extended later to allow for flexibility
            $table->date('end_date')->nullable();

            $table->unsignedDecimal('amount_to_pay', 8, 2)->nullable();
            $table->unsignedDecimal('amount_paid', 8, 2)->default(0);


            // Foreign keys

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_contracts');
    }
}
