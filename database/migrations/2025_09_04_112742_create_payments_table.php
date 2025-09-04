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
            $table->string('transid');
            $table->string('reference');
            $table->string('utilityref');
            $table->decimal('amount', 17, 2)->default(0.00);
            $table->string('msisdn');
            $table->string('vendor')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('user');
            $table->string('resultcode')->nullable();
            $table->string('result')->nullable();
            $table->string('message')->nullable();
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
