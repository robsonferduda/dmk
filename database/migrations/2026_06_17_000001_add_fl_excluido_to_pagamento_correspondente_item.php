<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlExcluidoToPagamentoCorrespondenteItem extends Migration
{
    public function up()
    {
        Schema::table('pagamento_correspondente_item_pai', function (Blueprint $table) {
            $table->char('fl_excluido_pai', 1)->default('N')->after('vl_despesa_pai');
        });
    }

    public function down()
    {
        Schema::table('pagamento_correspondente_item_pai', function (Blueprint $table) {
            $table->dropColumn('fl_excluido_pai');
        });
    }
}
