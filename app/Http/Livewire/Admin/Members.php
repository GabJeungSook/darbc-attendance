<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Filament\Forms;
use Filament\Tables;
use App\Models\Members as MembersModel;
use App\Models\Attendance;
use Carbon\Carbon;
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
            Filter::make('darbc_id')
                ->default()
                ->label('DARBC ID'),
            Filter::make('cluster'),
            Filter::make('status')
                ->label('Status'),
            Filter::make('percentage')
                ->label('Percentage'),
            Filter::make('last_name')
                ->default()
                ->label('Last Name'),
            Filter::make('user_first_name')
                ->default()
                ->label('First Name'),
            Filter::make('user_middle_name')
                ->label('Middle Name'),
            Filter::make('succession_number')
                ->default()
                ->label('Ownership'),
            Filter::make('date_of_birth')
                ->label('Date of Birth'),
            Filter::make('deceased_at')
                ->label('Date of Death'),
            Filter::make('place_of_birth')
                ->label('Place of Birth'),
            Filter::make('gender_name')
                ->label('Gender'),
            Filter::make('blood_type')
                ->label('Blood Type'),
            Filter::make('religion')
                ->label('Religion'),
            Filter::make('membership_status_name')
                ->label('Membership'),
            Filter::make('occupation_name')
                ->label('Occupation'),
            Filter::make('occupation_details')
                ->label('Occupation Details'),
            // Filter::make('region_description')
            //     ->label('Region'),
            Filter::make('address_line')
                ->label('Address'),
            Filter::make('civil_status')
                ->label('Civil Status'),
            Filter::make('mother_maiden_name')
                ->label("Mother's Maiden Name"),
            Filter::make('spouse')
                ->label('Name of Spouse'),
            Filter::make('children_list')
                ->label('Children'),
            Filter::make('dependents_count')
                ->label('No. of Dependents'),
            Filter::make('spa')
                ->label('SPA/Representatives')
                ->default(),
            Filter::make('sss_number')
                ->label('SSS'),
            Filter::make('tin_number')
                ->label('TIN'),
            Filter::make('philhealth_number')
                ->label('PhilHealth'),
            Filter::make('contact_number')
                ->label('Contact Number'),
            Filter::make('application_date')
                ->label('Date of Application'),
            Filter::make('area')
                ->label('Area')
                ->default(),
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
            ->visible(fn () => $this->tableFilters['darbc_id']['isActive'])
            ->label('DARBC ID')
            ->sortable(),
            Tables\Columns\TextColumn::make('last_name')
            ->visible(fn () => $this->tableFilters['last_name']['isActive'])
            ->label('Last Name')
            ->sortable(),
            Tables\Columns\TextColumn::make('first_name')
            ->visible(fn () => $this->tableFilters['user_first_name']['isActive'])
            ->label('First Name')
            ->sortable(),
            Tables\Columns\TextColumn::make('middle_name')
            ->visible(fn () => $this->tableFilters['user_middle_name']['isActive'])
            ->label('Middle Name')
            ->sortable(),
            Tables\Columns\BadgeColumn::make('succession_number')
                ->colors([
                    'success'
                ])
                ->sortable()
                ->formatStateUsing(fn ($state) => $state == 0 ? 'Original' : $this->ordinal($state) . ' Successor')
                ->visible(fn () => $this->tableFilters['succession_number']['isActive'])
                ->label('Ownership'),
            Tables\Columns\TextColumn::make('spa')
            ->label('SPA/Representatives')->sortable()->searchable()
            ->formatStateUsing(function ($state) {
                return $state ? implode("\n", json_decode($state, true)) : '';
            })->visible(fn () => $this->tableFilters['spa']['isActive']),
            Tables\Columns\TextColumn::make('area')
            ->visible(fn () => $this->tableFilters['area']['isActive'])
            ->label('Area')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('place_of_birth')
            ->visible(fn () => $this->tableFilters['place_of_birth']['isActive'])
            ->label('Place of Birth')
            ->sortable(),
            Tables\Columns\TextColumn::make('occupation')
            ->visible(fn () => $this->tableFilters['occupation_name']['isActive'])
            ->label('Occupation')
            ->sortable(),
            Tables\Columns\TextColumn::make('mothers_maiden_name')
            ->visible(fn () => $this->tableFilters['mother_maiden_name']['isActive'])
            ->label('Mother\'s Maiden Name')
            ->sortable(),
            Tables\Columns\TextColumn::make('sss_number')
            ->visible(fn () => $this->tableFilters['sss_number']['isActive'])
            ->label('SSS')
            ->sortable(),
            Tables\Columns\TextColumn::make('cluster')
            ->visible(fn () => $this->tableFilters['cluster']['isActive'])
            ->label('Cluster')
            ->sortable(),
            Tables\Columns\TextColumn::make('gender')
            ->visible(fn () => $this->tableFilters['gender_name']['isActive'])
            ->label('Gender')
            ->sortable(),
            Tables\Columns\TextColumn::make('occupation_details')
            ->visible(fn () => $this->tableFilters['occupation_details']['isActive'])
            ->label('Occupation Details')
            ->sortable(),
            Tables\Columns\TextColumn::make('spouse')
            ->visible(fn () => $this->tableFilters['spouse']['isActive'])
            ->label('Spouse')
            ->sortable(),
            Tables\Columns\TextColumn::make('tin_number')
            ->visible(fn () => $this->tableFilters['tin_number']['isActive'])
            ->label('TIN')
            ->sortable(),
            Tables\Columns\TextColumn::make('status')
            ->visible(fn () => $this->tableFilters['status']['isActive'])
            ->label('Status')
            ->sortable(),
            Tables\Columns\TextColumn::make('blood_type')
            ->visible(fn () => $this->tableFilters['blood_type']['isActive'])
            ->label('Blood Type')
            ->sortable(),
            Tables\Columns\TextColumn::make('children')
            ->visible(fn () => $this->tableFilters['children_list']['isActive'])
            ->label('Children')
            ->formatStateUsing(function ($state) {
                return $state ? implode("\n", json_decode($state, true)) : '';
            })
            ->sortable(),
            Tables\Columns\TextColumn::make('philhealth_number')
            ->visible(fn () => $this->tableFilters['philhealth_number']['isActive'])
            ->label('Philhealth')
            ->sortable(),
            Tables\Columns\TextColumn::make('percentage')
            ->visible(fn () => $this->tableFilters['percentage']['isActive'])
            ->label('Percentage')
            ->sortable(),
            Tables\Columns\TextColumn::make('date_of_birth')
            ->visible(fn () => $this->tableFilters['date_of_birth']['isActive'])
            ->label('Date of Birth')
            ->formatStateUsing(function ($state) {
                return Carbon::parse($state)->format('F j, Y') ?? '';
            })
            ->sortable(),
            Tables\Columns\TextColumn::make('religion')
            ->visible(fn () => $this->tableFilters['religion']['isActive'])
            ->label('Religion')
            ->sortable(),
            Tables\Columns\TextColumn::make('address_line')
            ->visible(fn () => $this->tableFilters['address_line']['isActive'])
            ->label('Address')
            ->sortable(),
            Tables\Columns\TextColumn::make('dependents_count')
            ->visible(fn () => $this->tableFilters['dependents_count']['isActive'])
            ->label('No. of Dependents')
            ->sortable(),
            Tables\Columns\TextColumn::make('contact_number')
            ->visible(fn () => $this->tableFilters['contact_number']['isActive'])
            ->label('Contact Number')
            ->sortable(),
            Tables\Columns\TextColumn::make('deceased_at')
            ->visible(fn () => $this->tableFilters['deceased_at']['isActive'])
            ->label('Date of Death')
            ->sortable(),
            Tables\Columns\TextColumn::make('membership_status')
            ->visible(fn () => $this->tableFilters['membership_status_name']['isActive'])
            ->label('Membership')
            ->sortable(),
            Tables\Columns\TextColumn::make('civil_status')
            ->visible(fn () => $this->tableFilters['civil_status']['isActive'])
            ->label('Civil Status')
            ->sortable(),
            Tables\Columns\TextColumn::make('application_date')
            ->visible(fn () => $this->tableFilters['application_date']['isActive'])
            ->label('Date of Application')
            ->formatStateUsing(function ($state) {
                return Carbon::parse($state)->format('F j, Y') ?? '';
            })
            ->sortable(),
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
