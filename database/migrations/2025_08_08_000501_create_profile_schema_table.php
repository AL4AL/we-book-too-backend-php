<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile_schema', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->json('fields'); // schema definition
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_schema');
    }
};
