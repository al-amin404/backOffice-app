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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->uuid('staff_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile');
            $table->date('date_of_birth');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->date('joining_date');
            $table->string('blood_group')->nullable();
            $table->string('nid')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
