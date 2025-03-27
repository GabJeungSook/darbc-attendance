<?php

namespace App\Http\Livewire\Admin;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Members;
use Livewire\Component;
use App\Models\Giveaway;
use App\Models\VoidMember;
use Mike42\Escpos\Printer;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Filament\Forms\Components\Radio;
use App\Models\Printer as ModelPrinter;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\CheckboxList;
use App\Models\Attendance as AttendanceModel;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Attendance extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;
    public $event;

    protected function getTableQuery(): Builder
    {
        return Members::query();
    }

    protected function getTableFiltersLayout(): ?string
    {
        return Layout::AboveContent;
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
        Filter::make('attendance')
            ->form([
                Forms\Components\Select::make('status')
                ->options([
                        'true' => 'Attended',
                        'false' => 'Not Attended',
                    ])->default('false'),

            ])
            ->query(function (Builder $query, array $data): Builder {
                if ($data['status'] == 'true') {
                    $query->whereHas('attendances', function ($query) {
                        $query->where('event_id', $this->event->id);
                    });
                } elseif ($data['status'] == 'false') {
                    $query->whereDoesntHave('attendances', function ($query) {
                        $query->where('event_id', $this->event->id);
                    });
                }
                return $query;
            }),
            // TernaryFilter::make('attendance')
            // ->placeholder('All')
            // ->trueLabel('Attended')
            // ->falseLabel('Not Attended')
            // ->options([
            //     'true' => 'Attended',
            //     'false' => 'Not Attended',
            // ])
            // ->queries(
            //     true: fn (Builder $query) => $query->whereHas('attendances', function ($query) {
            //         $query->where('event_id', $this->event->id);
            //     }),
            //     false: fn (Builder $query) => $query->whereDoesntHave('attendances', function ($query) {
            //         $query->where('event_id', $this->event->id);
            //     }),
            //     blank: fn (Builder $query) => $query,
            // )
        ];
    }

    protected function getTableActions()
    {
        return [
            Action::make('attend')
            ->label('Confirm')
            ->button()
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->form([
                Radio::make('giveaways')
                ->reactive()
                ->required()
                ->options(Giveaway::where('event_id', $this->event->id)->pluck('name', 'id')->toArray()),
                TextInput::make('other_specify')->visible(fn ($get) => $get('giveaways') == 7),
                ])
                ->action(function ($record, $data) {
                    $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                    if ($attendance) {
                        $this->dialog()->error(
                            $title = 'Oops!',
                            $description = 'Member Already Attended'
                        );
                    } else {
                        if($data['giveaways'] === '7')
                        {
                        $attendance_record = AttendanceModel::create([
                                'user_id' => auth()->user()->id,
                                'member_id' => $record->id,
                                'giveaway_id' => $data['giveaways'],
                                'other_giveaway' => $data['other_specify'],
                                'event_id' => $this->event->id,
                                'last_name' => $record->last_name,
                                'first_name' => $record->first_name,
                                'area' => $record->area,
                            ]);
                        }else{
                        $attendance_record =   AttendanceModel::create([
                                'user_id' => auth()->user()->id,
                                'member_id' => $record->id,
                                'giveaway_id' => $data['giveaways'],
                                'event_id' => $this->event->id,
                                'last_name' => $record->last_name,
                                'first_name' => $record->first_name,
                                'area' => $record->area,
                            ]);

                        }
                        if($this->event->has_printer === 1)
                        {
                            $this->printReceipt($attendance_record);
                        }

                        $this->dialog()->success(
                            $title = 'Success',
                            $description = 'Member Attended'
                        );
                    }
                })->visible(function  ($record) {
                        $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                        if ($attendance || $this->event->has_giveaway == false) {
                            return false;
                        } else {
                            return true;
                        }
                    }),
            Action::make('attend_wo_giveaway')
            ->label('Confirm')
            ->button()
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->action(function ($record, $data){
                $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                if ($attendance) {
                    $this->dialog()->error(
                        $title = 'Oops!',
                        $description = 'Member Already Attended'
                    );
                }else
                {
                    $attendance_record =   AttendanceModel::create([
                        'user_id' => auth()->user()->id,
                        'member_id' => $record->id,
                        'event_id' => $this->event->id,
                        'last_name' => $record->last_name,
                        'first_name' => $record->first_name,
                        'area' => $record->area,
                    ]);
                    if($this->event->has_printer === 1){
                        $this->printReceipt($attendance_record);
                    }


                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Member Attended'
                    );
                }

            })->requiresConfirmation()
            ->visible(function  ($record) {
                $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                if ($attendance || $this->event->has_giveaway == true) {
                    return false;
                } else {
                    return true;
                }
            }),
            Action::make('void')
            ->label('Void')
            ->icon('heroicon-o-x-circle')
            ->button()
            ->color('danger')
            ->visible(function  ($record) {
                $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                if ($attendance) {
                    return true;
                } else {
                    return false;
                }
            })
            ->action(function ($record) {
                //insert to void table
                DB::beginTransaction();
                VoidMember::create([
                    'darbc_id' => $record->darbc_id,
                    'last_name' => $record->last_name,
                    'first_name' => $record->first_name,
                    'area' => $record->area,
                ]);
                $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                $attendance->delete();
                DB::commit();
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Attendance Voided'
                );
            })->requiresConfirmation(),


            // Action::make('attended')
            // ->label('Confirm')
            // ->icon('heroicon-o-check-circle')
            // ->button()
            // ->color('success')
            // ->action(function ($record) {
            //     $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
            //     if ($attendance) {
            //         $this->dialog()->error(
            //             $title = 'Oops!',
            //             $description = 'Member Already Attended'
            //         );
            //     } else {
            //         AttendanceModel::create([
            //             'member_id' => $record->id,
            //             'event_id' => $this->event->id,
            //         ]);
            //         $this->dialog()->success(
            //             $title = 'Success',
            //             $description = 'Member Attended'
            //         );
            //     }
            // })->visible(function  ($record) {
            //     $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
            //     if ($attendance) {
            //         return false;
            //     } else {
            //         return true;
            //     }
            // })->requiresConfirmation(),
        ];
    }

    public function printReceipt($record)
    {
        $attendance = AttendanceModel::where('id', $record->id)->first();
        $active_event = \App\Models\Event::where('event_status', 1)->first();
        try{
           $printerIp = auth()->user()->printer->ip_address;
           $printerPort = 9100;
           $connector = new NetworkPrintConnector($printerIp);
           $printer = new Printer($connector);
           if($printer)
           $printer->setJustification(Printer::JUSTIFY_CENTER);
           $printer->setEmphasis(true);
           $printer -> text(strtoupper($active_event->event_name)."\n");
           $printer->setEmphasis(false);
           $printer -> text(\Carbon\Carbon::parse($active_event->date_of_event)->format('F d, Y')."\n");
           $printer -> text(strtoupper(auth()->user()->name)."\n");
           $printer -> feed(2);
           $printer->setJustification(Printer::JUSTIFY_LEFT);
           $printer -> text("DARBC ID: ".$attendance->member->darbc_id."\n");
          // $printer -> text("Name: ".$attendance->member->last_name." ".$attendance->member->first_name."\n");
           $printer -> text("Date: ".\Carbon\Carbon::parse($attendance->created_at)->format('F d, Y')."\n");
           $printer -> text("Time: ".\Carbon\Carbon::parse($attendance->created_at)->format('h:i:s A')."\n");
        //    if($attendance->giveaway->name == 'Other')
        //    {
        //     $printer -> text("Giveaway: ".$attendance->giveaway->name." (".$attendance->other_giveaway.")"."\n");
        //    }else{
        //     $printer -> text("Giveaway: ".$attendance->giveaway->name."\n");
        //    }
           $printer -> feed(4);
           $printer->setJustification(Printer::JUSTIFY_CENTER);
           $printer->setTextSize(1, 2);
           $printer -> text(strtoupper($attendance->member->last_name." ".$attendance->member->first_name)."\n");
           $printer -> feed(1);
           $printer -> cut();
           $printer -> close();
        }catch (\Exception $e) {
           $this->dialog()->error(
               $title = 'Oops!',
               $description = 'Failed to connect to the printer. Please check the IP Address.'
           );
       }
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
                //check if string or array
                if ($state == null || $state == '[]') {
                    return '';
                }
                if (is_string($state)) {
                    if (strpos($state, '[') !== false) {
                        return implode("\n", json_decode($state, true));
                    }else{
                        return $state;
                    }
                } else {
                    return implode("\n", json_decode($state, true));
                }
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
        ];
    }

    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }


    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();
    }
    public function render()
    {
        return view('livewire.admin.attendance');
    }
}
