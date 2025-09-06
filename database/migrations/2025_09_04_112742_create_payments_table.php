<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('operator');
            $table->string('transid')->unique();
            $table->string('reference')->unique();
            $table->string('utilityref'); //order id
            $table->decimal('amount', 17, 2)->default(0.00);
            $table->string('msisdn');
            $table->string('vendor')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('user');
            $table->string('email')->nullable(); //create a default email
            $table->string('name')->nullable();
            $table->string('currency')->nullable();
            $table->string('webhookurl')->nullable();
            $table->string('buyer_remark')->nullable();
            $table->string('merchant_remark')->nullable();
            $table->string('no_of_items')->nullable();
            $table->string('resultcode')->nullable();
            $table->string('result')->nullable();
            $table->string('message')->nullable();
            $table->boolean('response_success')->nullable();
            $table->integer('response_status')->nullable();
            $table->string('payment_token')->nullable();
            $table->text('payment_gateway_url')->nullable();
            $table->string('selcom_reference')->nullable();
            $table->text('channel')->nullable();
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
