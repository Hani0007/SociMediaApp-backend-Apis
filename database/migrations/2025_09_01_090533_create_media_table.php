<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media', function (Blueprint $table) {
            $table->id(); // MediaId
            $table->string('media_type', 50); // e.g., image, video
            $table->string('url');            // storage path or full URL
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('media');
    }
};
