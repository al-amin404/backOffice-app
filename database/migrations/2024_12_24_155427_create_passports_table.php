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
        Schema::create('passports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('passport');
            $table->string('mobile');
            $table->date('date_of_birth');
            $table->string('service');
            $table->string('country');
            $table->string('reference')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->string('status')->default('Received');
            $table->string('visaNumber')->nullable();
            $table->string('idNumber')->nullable();
            $table->string('rlNumber')->nullable();
            $table->text('note')->nullable();
            $table->string('passport_photo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passports');
    }
};
