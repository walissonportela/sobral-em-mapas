<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstatisticasRecomendadoCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            // Remove a coluna antiga 'recomendado'
            if (Schema::hasColumn('estatisticas', 'recomendado')) {
                $table->dropColumn('recomendado');
            }
             if (Schema::hasColumn('estatisticas', 'mapa_anterior')) {
                $table->dropColumn('mapa_anterior');
            }
            // Cria nova coluna 'mapa_recomendado' para armazenar nome do mapa recomendado
            $table->string('mapa_recomendado')->nullable()->after('mapa');
        });
    }

    public function down()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            // Restaura a coluna 'recomendado' como boolean, se necessário
            $table->boolean('recomendado')->default(false)->after('mapa');

            // Remove 'mapa_recomendado'
            $table->dropColumn('mapa_recomendado');
        });
    }
}
