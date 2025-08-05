<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('school_years', function (Blueprint $table) {
            // Drop the existing unique constraint on year
            $table->dropUnique('school_years_year_unique');
            
            // Add a new composite unique constraint
            $table->unique(['year', 'semester']);
        });
    }

    public function down()
    {
        Schema::table('school_years', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['year', 'semester']);
            
            // Restore the original unique constraint
            $table->unique('year');
        });
    }
};