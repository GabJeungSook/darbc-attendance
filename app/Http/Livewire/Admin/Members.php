<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Filament\Forms;
use Filament\Tables;
use App\Models\Members as MembersModel;
use App\Models\Attendance;
use WireUi\Traits\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Layout;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Filters\Filter;
use DB;

class Members extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;


    protected function getTableQuery(): Builder
    {
        return MembersModel::query();
    }

    protected function getTableHeaderActions()
    {
        return [
            //  Action::make('import')
            // ->icon('heroicon-o-download')
            // ->label('Import Data')
            // ->button()
            // ->color('warning')
            // ->action('redirectToUpload'),
            Action::make('update_members')
            ->icon('heroicon-o-upload')
            ->label('Update Members')
            ->button()
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {
                $url = 'https://darbcmembership.org/api/member-darbc-members';
                $response = Http::withOptions(['verify' => false])->get($url);
                $member_data = $response->json();

                $collection = collect($member_data);
                DB::beginTransaction();
                foreach ($collection as $item) {
                    // Ensure darbc_id exists in the item
                    if (!isset($item['darbc_id']) || strpos($item['darbc_id'], '.') !== false) {
                        continue; // Skip this iteration instead of returning null
                    }

                    // Retrieve values from $item array
                    $darbc_id = $item['darbc_id'];
                    $lastName = $item['surname'];
                    $firstName = $item['first_name'];
                    $succession = $item['succession_number'];
                    $spa = $item['spa'] ?? null;
                    $area = $item['area'] ?? null; // Handle missing area gracefully

                    // Find the member
                    $member = MembersModel::where('darbc_id', $darbc_id)->first();

                    if ($member !== null) {
                        // Check if any details have changed
                        if ($member->last_name !== $lastName || $member->first_name !== $firstName ||
                            $member->succession !== $succession || $member->spa !== $spa) {

                            // Update member details
                            $member->last_name = $lastName;
                            $member->first_name = $firstName;
                            $member->succession = $succession;
                            $member->spa = $spa;
                            $member->area = $area;
                            $member->save();
                        }
                    } else {
                        // Debug missing members
                        dd("Member not found with darbc_id: " . $darbc_id);
                    }
                }
                DB::commit();
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Members Updated Successfully'
                );

            }),
            Action::make('update_records')
            ->icon('heroicon-o-upload')
            ->label('Update Data')
            ->button()
            ->color('danger')
            ->visible(false)
            ->action(function ($record) {
                $attendance = Attendance::get();
                foreach ($attendance as $item) {
                    if($item->last_name == null || $item->first_name == null || $item->area == null)
                    {
                        $member = MembersModel::where('id', $item->member_id)->first();
                        $item->update([
                            'last_name' => $member->last_name,
                            'first_name' => $member->first_name,
                            'area' => $member->area,
                        ]);
                    }
                }
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Updated Successfully'
                );
            })
        ];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return Layout::AboveContent;
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    protected function getTableFilters(): array
    {
        return [
            //Filter::make('succession_number')->label('Succession')
        ];
    }

    protected function getTableActions()
    {
        return [
            EditAction::make('edit')
            ->icon('heroicon-o-pencil')
            ->label('Update')
            ->button()
            ->outlined()
            ->color('success')
            ->after(function () {
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Updated Successfully'
                );
            })
            ->form([
                Forms\Components\TextInput::make('last_name')->label("Last Name")->required(),
                Forms\Components\TextInput::make('first_name')->label("First Name")->required(),
                Forms\Components\TextInput::make('area')->label("Area")->required(),
            ])
            ->requiresConfirmation(),
        ];
    }


    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('darbc_id')
            ->label('DARBC ID')->sortable()->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('last_name')
            ->label('Last Name')->sortable()->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('first_name')
            ->label('First Name')->sortable()->searchable()->toggleable(),
            Tables\Columns\BadgeColumn::make('succession_number')
                ->colors([
                    'success'
                ])
                ->sortable()
                ->formatStateUsing(fn ($state) => $state == 0 ? 'Original' : $this->ordinal($state) . ' Successor')
                ->label('Ownership')->toggleable(),
            Tables\Columns\TextColumn::make('spa')
            ->label('SPA')->sortable()->searchable()->toggleable()
            ->formatStateUsing(function ($state) {
                return $state ? implode("\n", json_decode($state, true)) : '';
            })->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('area')
            ->label('Area')->sortable()->searchable()->toggleable(),
        ];
    }

    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }

    public function redirectToUpload()
    {
        return redirect()->route('upload');
    }

    public function render()
    {
        return view('livewire.admin.members');
    }
}
