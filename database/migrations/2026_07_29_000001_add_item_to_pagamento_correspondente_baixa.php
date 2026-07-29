<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemToPagamentoCorrespondenteBaixa extends Migration
{
    public function up()
    {
        Schema::table('pagamento_correspondente_baixa_pcb', function (Blueprint $table) {
            $table->unsignedBigInteger('cd_pagamento_correspondente_item_pai')->nullable()->after('cd_pagamento_correspondente_pag');

            $table->foreign('cd_pagamento_correspondente_item_pai', 'pcb_item_fk')
                ->references('cd_pagamento_correspondente_item_pai')
                ->on('pagamento_correspondente_item_pai')
                ->onDelete('cascade');

            $table->index('cd_pagamento_correspondente_item_pai', 'pcb_item_idx');
        });
    }

    public function down()
    {
        Schema::table('pagamento_correspondente_baixa_pcb', function (Blueprint $table) {
            $table->dropForeign('pcb_item_fk');
            $table->dropIndex('pcb_item_idx');
            $table->dropColumn('cd_pagamento_correspondente_item_pai');
        });
    }
}
