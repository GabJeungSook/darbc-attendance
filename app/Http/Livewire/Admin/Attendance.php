<?php

namespace App\Http\Livewire\Admin;

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
                        $this->printReceipt($attendance_record);
                        $this->dialog()->success(
                            $title = 'Success',
                            $description = 'Member Attended'
                        );
                    }
                })->visible(function  ($record) {
                        $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                        if ($attendance) {
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
           $printer -> text("Name: ".$attendance->member->last_name.", ".$attendance->member->first_name."\n");
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
           $printer -> text(strtoupper($attendance->member->last_name.", ".$attendance->member->first_name)."\n");
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
            ->label('DARBC ID')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('last_name')
            ->label('Last Name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('first_name')
            ->label('First Name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('area')
            ->label('Area')->sortable()->searchable(),
        ];
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
