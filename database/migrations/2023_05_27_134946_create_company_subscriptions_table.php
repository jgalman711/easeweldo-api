<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('subscription_id');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('amount', 8, 2)->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
    }
};
