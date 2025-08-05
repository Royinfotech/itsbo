<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('year')->unique();
            $table->enum('semester', ['1st', '2nd']);
            $table->boolean('is_open')->default(false);
            $table->integer('officer_limit')->default(1);
            $table->json('open_positions')->nullable();
            $table->boolean('positions_locked')->default(false);
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('is_open');
            $table->index('year');
            
            // Foreign key for who opened the school year
            $table->foreign('opened_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};