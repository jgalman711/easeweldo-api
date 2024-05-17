<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approver', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approver_employee_id');
            $table->unsignedBigInteger('requester_employee_id');
            $table->string('request_type');
            $table->integer('order')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('approver_employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('requester_employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approver');
    }
};
