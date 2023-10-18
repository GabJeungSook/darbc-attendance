<?php

namespace App\Imports;

use App\Models\Members;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AreaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $darbc_id = $row['darbc_id'];
        $area = $row['area'];

        if (strpos($darbc_id, '.') !== false) {
            return null;
        }

        $member = Members::where('darbc_id', $darbc_id)->first();
        if($member != null)
        {
            if($member->area != $area)
            {
                $member->area = $area;
                $member->save();
            }
            else
            {
                return null;
            }
        }
    }
}
