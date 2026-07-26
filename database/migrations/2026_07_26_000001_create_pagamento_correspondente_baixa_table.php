<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagamentoCorrespondenteBaixaTable extends Migration
{
    public function up()
    {
        Schema::create('pagamento_correspondente_baixa_pcb', function (Blueprint $table) {
            $table->bigIncrements('cd_pagamento_correspondente_baixa_pcb');
            $table->unsignedBigInteger('cd_pagamento_correspondente_pag');
            $table->unsignedTinyInteger('cd_tipo_baixa_pcb'); // 1=Honorário 2=Despesa
            $table->decimal('vl_baixa_pcb', 12, 2);
            $table->date('dt_baixa_pcb');
            $table->text('ds_observacao_pcb')->nullable();
            $table->string('dc_comprovante_pcb', 255)->nullable();
            $table->timestamps();

            $table->foreign('cd_pagamento_correspondente_pag', 'pcb_pag_fk')
                ->references('cd_pagamento_correspondente_pag')
                ->on('pagamento_correspondente_pag')
                ->onDelete('cascade');

            $table->index('cd_pagamento_correspondente_pag', 'pcb_pag_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagamento_correspondente_baixa_pcb');
    }
}
