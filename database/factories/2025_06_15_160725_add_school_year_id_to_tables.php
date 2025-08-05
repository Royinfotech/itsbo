<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->foreignId('school_year_id')->nullable()->constrained('school_years');
    });

    Schema::table('attendances', function (Blueprint $table) {
        $table->foreignId('school_year_id')->nullable()->constrained('school_years');
    });

    Schema::table('events', function (Blueprint $table) {
        $table->foreignId('school_year_id')->nullable()->constrained('school_years');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['school_year_id']);
        $table->dropColumn('school_year_id');
    });

    Schema::table('attendances', function (Blueprint $table) {
        $table->dropForeign(['school_year_id']);
        $table->dropColumn('school_year_id');
    });

    Schema::table('events', function (Blueprint $table) {
        $table->dropForeign(['school_year_id']);
        $table->dropColumn('school_year_id');
    });
}
};
