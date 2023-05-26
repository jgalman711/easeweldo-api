<?php

use App\Models\Period;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->nullable();
            $table->integer('company_period_number');
            $table->enum('type', Period::TYPES);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', Period::STATUSES);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};