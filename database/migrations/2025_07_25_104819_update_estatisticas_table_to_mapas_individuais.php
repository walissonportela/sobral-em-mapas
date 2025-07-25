<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstatisticasTableToMapasIndividuais extends Migration
{
    public function up()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            // Adiciona novas colunas
            $table->string('mapa')->nullable()->after('ip_usuario');
            $table->integer('tempo')->nullable()->after('mapa');
            $table->boolean('recomendado')->default(false)->after('tempo');

            // Remove as antigas colunas agregadas
            $table->dropColumn('mapas_selecionados');
            $table->dropColumn('recommended_map_activations');
        });
    }

    public function down()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            // Volta ao modelo anterior
            $table->longText('mapas_selecionados')->after('ip_usuario');
            $table->longText('recommended_map_activations')->nullable()->after('mapas_selecionados');

            // Remove colunas individuais
            $table->dropColumn('mapa');
            $table->dropColumn('tempo');
            $table->dropColumn('recomendado');
        });
    }
}

