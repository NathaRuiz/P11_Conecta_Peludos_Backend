<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->string('breed');
            $table->enum('gender', ['Macho', 'Hembra']);
            $table->enum('size', ['Pequeño', 'Mediano', 'Grande', 'Gigante']);
            $table->enum('age', ['Cachorro', 'Adulto', 'Senior']);
            $table->string('approximate_age');
            $table->enum('status', ['Urgente', 'Disponible', 'En Acogida', 'Reservado', 'Adoptado']);
            $table->string('my_story');
            $table->string('description');
            $table->string('delivery_options');
            $table->string('image_url')->nullable();
            $table->string('public_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
