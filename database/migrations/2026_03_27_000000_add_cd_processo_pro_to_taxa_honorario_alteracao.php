<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCdProcessoProToTaxaHonorarioAlteracao extends Migration
{
    public function up()
    {
        Schema::table('taxa_honorario_alteracao_tha', function (Blueprint $table) {
            $table->unsignedBigInteger('cd_processo_pro')->nullable()->after('cd_taxa_honorario_entidade_the');
            $table->foreign('cd_processo_pro', 'tha_processo_pro_fk')
                  ->references('cd_processo_pro')
                  ->on('processo_pro');
        });
    }

    public function down()
    {
        Schema::table('taxa_honorario_alteracao_tha', function (Blueprint $table) {
            $table->dropForeign('tha_processo_pro_fk');
            $table->dropColumn('cd_processo_pro');
        });
    }
}
