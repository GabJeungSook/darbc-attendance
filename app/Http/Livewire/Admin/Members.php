<?php

namespace App\Http\Livewire\Admin;

use DB;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Attendance;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Illuminate\Support\Facades\Http;
use App\Models\Members as MembersModel;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
            Action::make('update_tin_status')
            ->icon('heroicon-o-upload')
            ->label('Update TIN Verification Status')
            ->button()
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {
                $url = 'https://darbcmembership.org/api/member-darbc-members-complete';
                $response = Http::withOptions(['verify' => false])->get($url);
                $member_data = $response->json();

                $collection = collect($member_data);
                DB::beginTransaction();

                $members = MembersModel::whereIn('darbc_id', $collection->pluck('darbc_id'))->get()->keyBy('darbc_id');

                foreach ($collection as $item) {
                    if (!isset($item['darbc_id']) || strpos($item['darbc_id'], '.') !== false) {
                        continue;
                    }

                    $darbc_id = $item['darbc_id'];
                    $member = $members->get($darbc_id);

                    if ($member) {
                        $updateData = [
                            'tin_verification_status' => $item['tin_verification_status'] ?? null,
                        ];

                        $member->update($updateData);
                    } else {
                        // Debug missing members
                        dd("Member not found with darbc_id: " . $darbc_id);
                    }
                }

                DB::commit();

                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'TIN Verification Status Updated Successfully'
                );
            })->visible(false),
            Action::make('update_names')
            ->icon('heroicon-o-upload')
            ->label('Update Member Names & Successions')
            ->button()
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {

                $url = 'https://darbcmembership.org/api/member-darbc-names?status=1';
                $response = Http::withOptions(['verify' => false])->get($url);
                $member_data = $response->json();

                $collection = collect($member_data);
                DB::beginTransaction();

                $members = MembersModel::whereIn('darbc_id', $collection->pluck('darbc_id'))->get()->keyBy('darbc_id');

                foreach ($collection as $item) {
                    if (!isset($item['darbc_id']) || strpos($item['darbc_id'], '.') !== false) {
                        continue;
                    }

                    $darbc_id = $item['darbc_id'];
                    $member = $members->get($darbc_id);

                    if ($member) {
                        $status = $item['rsbsa_record_id'] != null ? 'REGISTERED' : 'UNREGISTERED';
                        $is_complete = $item['has_missing_details'] == 0 ? 'COMPLETE' : 'INCOMPLETE';

                        //if member status is registered and has missing details, update the rsbsa status to incomplete
                        if ($status == 'REGISTERED' && $is_complete == 'INCOMPLETE') {
                            $rsbsa_status = 'INCOMPLETE';
                        } else {
                            $rsbsa_status = $status;
                        }
                        $updateData = [
                            'last_name' => $item['surname'],
                            'first_name' => $item['first_name'],
                            'middle_name' => $item['middle_name'],
                            'succession' => $item['succession_number'],
                            'tin_verification_status' => $item['tin_verification_status'],
                            'rsbsa_status' => $rsbsa_status,
                        ];

                        $member->update($updateData);
                    } else {
                        // Debug missing members
                        dd("Member not found with darbc_id: " . $darbc_id);
                    }
                }

                DB::commit();

                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Member Names Updated Successfully'
                );
            })->visible(true),
            Action::make('update_members')
            ->icon('heroicon-o-upload')
            ->label('Update Members')
            ->button()
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {
                $url = 'https://darbcmembership.org/api/member-darbc-members-complete';
                $response = Http::withOptions(['verify' => false])->get($url);
                $member_data = $response->json();

                $collection = collect($member_data);
                DB::beginTransaction();

                $members = MembersModel::whereIn('darbc_id', $collection->pluck('darbc_id'))->get()->keyBy('darbc_id');

                foreach ($collection as $item) {
                    if (!isset($item['darbc_id']) || strpos($item['darbc_id'], '.') !== false) {
                        continue;
                    }

                    $darbc_id = $item['darbc_id'];
                    $member = $members->get($darbc_id);

                    if ($member) {
                        $updateData = [
                            'last_name' => $item['surname'],
                            'first_name' => $item['first_name'],
                            'middle_name' => $item['middle_name'],
                            'succession' => $item['succession_number'],
                            'spa' => $item['spa'] ?? null,
                            'area' => $item['area'] ?? null,
                            'place_of_birth' => $item['place_of_birth'] ?? null,
                            'occupation' => $item['occupation'] ?? null,
                            'mothers_maiden_name' => $item['mother_maiden_name'] ?? null,
                            'sss_number' => $item['sss_number'] ?? null,
                            'cluster' => $item['cluster'] ?? null,
                            'gender' => $item['gender'] ?? null,
                            'occupation_details' => $item['occupation_details'] ?? null,
                            'spouse' => $item['spouse'] ?? null,
                            'tin_number' => $item['tin_number'] ?? null,
                            'tin_verification_status' => $item['tin_verification_status'] ?? null,
                            'status' => $item['status'] ?? null,
                            'blood_type' => $item['blood_type'] ?? null,
                            'children' => $item['children'] ?? null,
                            'philhealth_number' => $item['philhealth_number'] ?? null,
                            'percentage' => $item['percentage'] ?? null,
                            'date_of_birth' => isset($item['date_of_birth']) ? Carbon::parse($item['date_of_birth'])->format('Y-m-d') : null,
                            'religion' => $item['religion'] ?? null,
                            'address_line' => $item['address_line'] ?? null,
                            'dependents_count' => $item['dependents_count'] ?? null,
                            'contact_number' => $item['contact_number'] ?? null,
                            'deceased_at' => $item['deceased_at'] ?? null,
                            'membership_status' => $item['membership_status'] ?? null,
                            'civil_status' => $item['civil_status'] ?? null,
                            'application_date' => isset($item['application_date']) ? Carbon::parse($item['application_date'])->format('Y-m-d') : null,
                        ];

                        $member->update($updateData);
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
            Filter::make('tin_verification_status')
                ->label('TIN Verification Status')
                ->default(),
            Filter::make('rsbsa_status')
                ->label('RSBSA Status')
                ->default(),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 4;
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
            EditAction::make('edit_status')
            ->icon('heroicon-o-pencil')
            ->label('Update RSBSA Status')
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
                Forms\Components\Select::make('rsbsa_status')->label("RSBSA Status")
                ->options([
                    'REGISTERED' => 'REGISTERED',
                    'UNREGISTERED' => 'UNREGISTERED',
                    'INCOMPLETE' => 'INCOMPLETE',
                    'COMPLETE' => 'COMPLETE',
                    'MISSING DOCUMENTS' => 'MISSING DOCUMENTS',
                ])->required(),
            ])
            ->requiresConfirmation(),

        ];
    }


    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('darbc_id')
            ->visible(fn () => $this->tableFilters['darbc_id']['isActive'])
            ->label('DARBC ID')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('last_name')
            ->visible(fn () => $this->tableFilters['last_name']['isActive'])
            ->label('Last Name')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('first_name')
            ->visible(fn () => $this->tableFilters['user_first_name']['isActive'])
            ->label('First Name')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('middle_name')
            ->visible(fn () => $this->tableFilters['user_middle_name']['isActive'])
            ->label('Middle Name')->searchable()
            ->sortable(),
            Tables\Columns\BadgeColumn::make('succession')
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
                $decodedState = json_decode($state, true);
                if (is_array($decodedState)) {
                    return implode("\n", $decodedState);
                }
                
                return '';
            })
            ->visible(fn () => $this->tableFilters['spa']['isActive']),
            Tables\Columns\TextColumn::make('area')
            ->visible(fn () => $this->tableFilters['area']['isActive'])
            ->label('Area')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('place_of_birth')
            ->visible(fn () => $this->tableFilters['place_of_birth']['isActive'])
            ->label('Place of Birth')
            ->sortable(),
            Tables\Columns\TextColumn::make('occupation')
            ->visible(fn () => $this->tableFilters['occupation_name']['isActive'])
            ->label('Occupation')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('mothers_maiden_name')
            ->visible(fn () => $this->tableFilters['mother_maiden_name']['isActive'])
            ->label('Mother\'s Maiden Name')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('sss_number')
            ->visible(fn () => $this->tableFilters['sss_number']['isActive'])
            ->label('SSS')->searchable()
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
            ->label('Occupation Details')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('spouse')
            ->visible(fn () => $this->tableFilters['spouse']['isActive'])
            ->label('Spouse')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('tin_number')
            ->visible(fn () => $this->tableFilters['tin_number']['isActive'])
            ->label('TIN')->searchable()
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
            ->label('Children')->searchable()
            ->formatStateUsing(function ($state) {
                return $state ? implode("\n", json_decode($state, true)) : '';
            })
            ->sortable(),
            Tables\Columns\TextColumn::make('philhealth_number')
            ->visible(fn () => $this->tableFilters['philhealth_number']['isActive'])
            ->label('Philhealth')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('percentage')
            ->visible(fn () => $this->tableFilters['percentage']['isActive'])
            ->label('Percentage')
            ->sortable(),
            Tables\Columns\TextColumn::make('date_of_birth')
            ->visible(fn () => $this->tableFilters['date_of_birth']['isActive'])
            ->label('Date of Birth')
            ->formatStateUsing(function ($state) {
                if($state != null)
                {
                    return Carbon::parse($state)->format('F j, Y');
                }else{
                    return '';
                }
            })
            ->sortable(),
            Tables\Columns\TextColumn::make('religion')
            ->visible(fn () => $this->tableFilters['religion']['isActive'])
            ->label('Religion')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('address_line')
            ->visible(fn () => $this->tableFilters['address_line']['isActive'])
            ->label('Address')->searchable()
            ->sortable(),
            Tables\Columns\TextColumn::make('dependents_count')
            ->visible(fn () => $this->tableFilters['dependents_count']['isActive'])
            ->label('No. of Dependents')
            ->sortable(),
            Tables\Columns\TextColumn::make('contact_number')
            ->visible(fn () => $this->tableFilters['contact_number']['isActive'])
            ->label('Contact Number')->searchable()
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
                if($state != null)
                {
                    return Carbon::parse($state)->format('F j, Y');
                }else{
                    return '';
                }
            })
            ->sortable(),
            Tables\Columns\TextColumn::make('tin_verification_status')
            ->visible(fn () => $this->tableFilters['tin_verification_status']['isActive'])
            ->label('TIN Verification Status')
            ->sortable(),
            Tables\Columns\TextColumn::make('rsbsa_status')
            ->visible(fn () => $this->tableFilters['rsbsa_status']['isActive'])
            ->label('RSBSA Status')
            ->sortable(),
            Tables\Columns\ToggleColumn::make('is_restricted')
            ->label('Restrict Member')
            //->visible(fn () => $this->tableFilters['rsbsa_status']['isActive'])
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
