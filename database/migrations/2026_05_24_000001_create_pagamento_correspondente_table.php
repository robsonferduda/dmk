<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagamentoCorrespondenteTable extends Migration
{
    public function up()
    {
        Schema::create('pagamento_correspondente_pag', function (Blueprint $table) {
            $table->bigIncrements('cd_pagamento_correspondente_pag');
            $table->unsignedInteger('cd_conta_con');                   // escritório
            $table->unsignedInteger('cd_correspondente_cor');           // correspondente
            $table->unsignedSmallInteger('nu_mes_pag');                 // 1–12
            $table->unsignedSmallInteger('nu_ano_pag');                 // ex: 2026
            $table->decimal('vl_total_pag', 12, 2)->default(0);
            $table->unsignedTinyInteger('cd_status_pag')->default(1);   // 1=Gerado 2=Enviado 3=Aprovado 4=Pago
            $table->timestamp('dt_envio_aprovacao_pag')->nullable();
            $table->timestamp('dt_aprovacao_pag')->nullable();
            $table->timestamp('dt_pagamento_pag')->nullable();
            $table->text('ds_observacao_pag')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cd_conta_con')->references('cd_conta_con')->on('conta_con');
            $table->unique(['cd_conta_con', 'cd_correspondente_cor', 'nu_mes_pag', 'nu_ano_pag'], 'pag_unique_mes_ano');
        });

        Schema::create('pagamento_correspondente_item_pai', function (Blueprint $table) {
            $table->bigIncrements('cd_pagamento_correspondente_item_pai');
            $table->unsignedBigInteger('cd_pagamento_correspondente_pag');
            $table->unsignedBigInteger('cd_processo_pro')->nullable();
            $table->unsignedBigInteger('cd_processo_taxa_honorario_pth')->nullable();
            $table->unsignedBigInteger('cd_processo_despesa_pde')->nullable();
            $table->string('ds_descricao_pai', 255)->nullable();
            $table->decimal('vl_honorario_pai', 12, 2)->default(0);
            $table->decimal('vl_despesa_pai', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('cd_pagamento_correspondente_pag', 'pai_pag_fk')
                  ->references('cd_pagamento_correspondente_pag')
                  ->on('pagamento_correspondente_pag')
                  ->onDelete('cascade');

            $table->foreign('cd_processo_pro', 'pai_processo_fk')
                  ->references('cd_processo_pro')
                  ->on('processo_pro');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagamento_correspondente_item_pai');
        Schema::dropIfExists('pagamento_correspondente_pag');
    }
}
