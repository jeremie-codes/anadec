<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_jours');
            $table->text('motif');
            $table->enum('type', ['annuel', 'maladie', 'maternite', 'paternite', 'exceptionnel'])->default('annuel');
            $table->enum('statut', ['en_attente', 'approuve_directeur', 'valide_drh', 'rejete', 'traiter_hr'])->default('en_attente');
            $table->text('commentaire_directeur')->nullable();
            $table->text('commentaire_drh')->nullable();
            $table->date('date_approbation_directeur')->nullable();
            $table->date('date_validation_drh')->nullable();
            $table->date('date_traiter_rh')->nullable();
            $table->foreignId('approuve_par_directeur')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('valide_par_drh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('traiter_par_rh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['agent_id', 'statut']);
            $table->index('date_debut');
            $table->index('date_fin');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
