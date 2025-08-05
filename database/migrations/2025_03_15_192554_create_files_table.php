<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name'); // Stores the file name
            $table->string('file_path'); // Stores the file path
            $table->date('uploaded_date')->default(now()); // Stores the upload date
            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('files');
    }
};

