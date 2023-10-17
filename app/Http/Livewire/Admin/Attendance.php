<?php

namespace App\Http\Livewire\Admin;

use Filament\Forms;
use Filament\Tables;
use App\Models\Members;
use Livewire\Component;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\Attendance as AttendanceModel;

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
            Action::make('attended')
            ->label('Confirm')
            ->icon('heroicon-o-check-circle')
            ->button()
            ->color('success')
            ->action(function ($record) {
                $attendance = AttendanceModel::where('member_id', $record->id)->where('event_id', $this->event->id)->first();
                if ($attendance) {
                    $this->dialog()->error(
                        $title = 'Oops!',
                        $description = 'Member Already Attended'
                    );
                } else {
                    AttendanceModel::create([
                        'member_id' => $record->id,
                        'event_id' => $this->event->id,
                    ]);
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
            })->requiresConfirmation(),
        ];
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
