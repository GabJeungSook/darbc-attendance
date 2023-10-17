<?php

namespace App\Http\Livewire\Admin;

use App\Models\Members;
use Carbon\Carbon;
use League\Csv\Reader;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Upload extends Component
{
    use WithFileUploads;
    use Actions;
    public $masterlist;

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
            Members::create($member);

            $this->dialog()->success(
                $title = 'Success',
                $description = 'Data uploaded'
            );
        }


    }
    public function render()
    {
        return view('livewire.admin.upload');
    }
}
