<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecommendedMapActivationsToEstatisticas extends Migration
{
  
    public function up()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            $table->json('recommended_map_activations')->nullable()->after('mapas_selecionados');
        });
    }

    public function down()
    {
        Schema::table('estatisticas', function (Blueprint $table) {
            $table->dropColumn('recommended_map_activations');
        });
    }
}
