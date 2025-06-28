<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->enum('sexe', ['M', 'F']);
            $table->string('situation_matrimoniale');
            $table->string('direction');
            $table->string('service');
            $table->date('date_recrutement');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->enum('statut', [
                'actif', 'retraite', 'malade', 'demission', 'revocation',
                'disponibilite', 'detachement', 'mutation', 'reintegration',
                'mission', 'deces'
            ])->default('actif');

            // Dates spécifiques selon le statut
            $table->date('date_retraite')->nullable();
            $table->date('date_maladie')->nullable();
            $table->date('date_demission')->nullable();
            $table->date('date_revocation')->nullable();
            $table->date('date_disponibilite')->nullable();
            $table->date('date_detachement')->nullable();
            $table->date('date_mutation')->nullable();
            $table->date('date_reintegration')->nullable();
            $table->date('date_mission')->nullable();
            $table->date('date_deces')->nullable();

            $table->text('motif_changement_statut')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
