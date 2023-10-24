<?php

namespace App\Imports;

use App\Models\Members;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MemberImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $names = explode(',', $row['member_name']);
        $darbc_id = $row['darbc_id'];
        $lastName = trim($names[0]);
        $firstName = trim($names[1]);
        $succession = $row['succession'];
        $spa = $row['spa'];

        //if darbc_id has a dot, do not import
        if (strpos($darbc_id, '.') !== false) {
            return null;
        }

        $member = Members::where('darbc_id', $darbc_id)->first();
        if($member != null)
        {
            if($member->last_name != $lastName || $member->first_name != $firstName || $member->succession != $succession || $member->spa != $spa)
            {
                $member->last_name = $lastName;
                $member->first_name = $firstName;
                $member->succession = $succession;
                $member->spa = $spa;
                $member->save();
            }
            else
            {
                return null;
            }
        }else{
            dd($darbc_id);
        }

    }
}
