<?php

namespace App\Imports;

use App\Vara;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;

class VaraImport implements ToModel, WithValidation, SkipsOnError
{
      use Importable, SkipsErrors;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Vara([
            'nm_vara_var'   => $row[0],
            'cd_conta_con'  => \Session::get('SESSION_CD_CONTA')
        ]);
    }
}
