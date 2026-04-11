<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maquinas_credenciais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vmid')->unique();
            $table->string('login');
            $table->string('senha');
            $table->string('chave_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinas_credenciais');
    }
};
