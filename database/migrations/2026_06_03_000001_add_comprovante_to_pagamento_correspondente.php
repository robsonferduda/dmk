<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComprovanteToPagementoCorrespondente extends Migration
{
    public function up()
    {
        Schema::table('pagamento_correspondente_pag', function (Blueprint $table) {
            $table->string('dc_comprovante_pag')->nullable()->after('ds_observacao_pag');
        });
    }

    public function down()
    {
        Schema::table('pagamento_correspondente_pag', function (Blueprint $table) {
            $table->dropColumn('dc_comprovante_pag');
        });
    }
}
