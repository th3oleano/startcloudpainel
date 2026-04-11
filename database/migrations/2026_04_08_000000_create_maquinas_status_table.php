<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maquinas_status', function (Blueprint $table) {
            $table->id();
            $table->integer('vmid')->unique();
            $table->string('status')->default('livre'); // livre ou indisponivel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinas_status');
    }
};
