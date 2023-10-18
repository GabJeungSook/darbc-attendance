<?php

namespace App\Http\Livewire\Admin;

use App\Models\Members;
use Carbon\Carbon;
use League\Csv\Reader;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Imports\MemberImport;
use App\Imports\AreaImport;
use Maatwebsite\Excel\Facades\Excel;

class Upload extends Component
{
    use WithFileUploads;
    use Actions;
    public $masterlist;
    public $area;
    public $member_update;
    public $area_update;

    public function uploadMembers()
    {
        $csvContents = Storage::get($this->masterlist->getClientOriginalName());
        $csvReader = Reader::createFromString($csvContents);
        $csvRecords = $csvReader->getRecords();
        foreach ($csvRecords as $csvRecord) {

            $member = [
                'darbc_id' => $csvRecord[0],
                'last_name' => $csvRecord[1],
                'first_name' => $csvRecord[2],
                'succession' => $csvRecord[3],
                'spa' => $csvRecord[4],
            ];


            foreach ($member as $key => $value) {
                if (empty(trim($value)) && $value !== '0') {
                    $member[$key] = null;
                }
            }
            if (strpos($member['darbc_id'], '.') !== false) {
                continue;
            }

            Members::create($member);

            $this->dialog()->success(
                $title = 'Success',
                $description = 'Data uploaded'
            );
        }
    }

    public function returnToMembers()
    {
        return redirect()->route('members');
    }

    public function updateMember()
    {
        Excel::import(new MemberImport, $this->member_update);

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Data updated'
        );
    }

    public function updateArea()
    {
        Excel::import(new AreaImport, $this->area_update);

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Data updated'
        );
    }

    public function uploadArea()
    {
        $csvContents = Storage::get($this->area->getClientOriginalName());
        $csvReader = Reader::createFromString($csvContents);
        $csvRecords = $csvReader->getRecords();
        foreach ($csvRecords as $csvRecord) {

                $area = [
                    'darbc_id' => $csvRecord[0],
                    'area' => $csvRecord[1],
                ];
                Members::where('darbc_id', $area['darbc_id'])->update(['area' => $area['area']]);
        }

        $this->dialog()->success(
            $title = 'Success',
            $description = 'Data saved'
        );

    }

    public function resetArea()
    {
        Members::query()->update(['area' => null]);
        $this->dialog()->success(
            $title = 'Success',
            $description = 'Data reset'
        );
    }
    public function render()
    {
        return view('livewire.admin.upload');
    }
}
