<?php

namespace App\Imports;

use App\Vara;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class VaraImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Vara([
            'nm_vara_var'   => $row['lista_de_varas'],
            'cd_conta_con'  => \Session::get('SESSION_CD_CONTA')
        ]);
    }

    public function onError(\Throwable $e)
    {
        dd($e);
    }
}
