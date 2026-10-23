<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herramientas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 80);
            $table->text('descripcion');
            $table->string('categoria', 20)->index();
            $table->string('barrio', 60);
            $table->decimal('latitud', 9, 6);
            $table->decimal('longitud', 9, 6);
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('herramientas');
    }
};
