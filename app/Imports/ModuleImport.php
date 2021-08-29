<?php

namespace App\Imports;

use App\Module;
use Maatwebsite\Excel\Concerns\ToModel;

class ModuleImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Module([
            //
        ]);
    }
}
