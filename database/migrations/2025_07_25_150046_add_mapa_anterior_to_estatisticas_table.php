<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapaAnteriorToEstatisticasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('estatisticas', function (Blueprint $table) {
        $table->string('mapa_anterior')->nullable()->after('mapa');
    });
}

public function down()
{
    Schema::table('estatisticas', function (Blueprint $table) {
        $table->dropColumn('mapa_anterior');
    });
}

}
