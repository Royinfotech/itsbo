<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name');
            $table->string('event_location');
            $table->date('event_date');
            $table->enum('time_duration', ['Whole Day', 'Half Day: Morning', 'Half Day: Afternoon']);
            $table->timestamps(); // Includes created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

