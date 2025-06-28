<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('numero_permis')->unique();
            $table->string('categorie_permis'); // A, B, C, D, etc.
            $table->date('date_obtention_permis');
            $table->date('date_expiration_permis');
            $table->integer('experience_annees')->default(0);
            $table->enum('statut', ['actif', 'suspendu', 'inactif'])->default('actif');
            $table->text('observations')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('statut');
            $table->index('disponible');
            $table->index('categorie_permis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chauffeurs');
    }
};
