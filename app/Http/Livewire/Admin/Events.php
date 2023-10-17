<?php

namespace App\Http\Livewire\Admin;

use Filament\Forms;
use Filament\Tables;
use App\Models\Event;
use Livewire\Component;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;


class Events extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;

    protected function getTableQuery(): Builder
    {
        return Event::query();
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make('save')
            ->disableCreateAnother()
            ->modalHeading('Add new')
            ->modalButton('Save')
            ->after(function () {
                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Saved Successfully'
                );
            })
            ->label('Add New')
            ->button()
            ->color('primary')
            ->icon('heroicon-o-plus')
            ->form([
                Forms\Components\TextInput::make('event_name')->label("Event Name")->required(),
                Forms\Components\Datepicker::make('date_of_event')->label("Date")->required(),
            ])
            ->requiresConfirmation()
        ];
    }

    protected function getTableActions()
    {
        return [
            ActionGroup::make([
                EditAction::make('edit')
                ->icon('heroicon-o-pencil')
                ->label('Update')

                ->color('success')
                ->after(function () {
                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Updated Successfully'
                    );
                })
                ->form([
                    Forms\Components\TextInput::make('event_name')->label("Event Name")->required(),
                    Forms\Components\Datepicker::make('date_of_event')->label("Date")->required(),
                ])
                ->requiresConfirmation(),
                DeleteAction::make('delete')
                ->icon('heroicon-o-trash')
                ->label('Delete')
                ->color('danger')
                ->after(function () {
                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Record Deleted'
                    );
                })
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->event_status != 1),
                ])


        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('event_name')
            ->label('Name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('date_of_event')
            ->label('Date')
            ->date('F d, Y')->sortable()->searchable(),
            Tables\Columns\ToggleColumn::make('event_status')
            ->label('ACTIVE')
            ->onColor('success')
            ->onIcon('heroicon-s-check')
            ->offIcon('heroicon-s-x')
            ->offColor('danger')
            ->updateStateUsing(function ($record, $state) {
                //activate 1 event only at a time and display a notification if there is an active event
                $activeElection = Event::where('event_status', true)->exists();
                if($record->event_status)
                {
                    $record->update(['event_status' => false]);
                }else{
                    if($activeElection)
                    {
                        Notification::make()
                        ->title('Operation Failed')
                        ->body('You can only activate one (1) event at a time.')
                        ->danger()
                        ->send();
                        $this->redirect(route('event'));
                    } else {
                        $record->event_status == false ? $record->update(['event_status' => true]) : $record->update(['event_status' => false]);
                    }
                }

                // $activeElection = Event::where('event_status', true)->exists();
                // if($record->is_active)
                // {
                //     $record->update(['event_status' => false]);
                // }else{
                //     if($activeElection)
                //     {
                //         Notification::make()
                //         ->title('Operation Failed')
                //         ->body('You can only activate one (1) event at a time.')
                //         ->danger()
                //         ->send();
                //         $this->redirect(route('event'));
                //     } else {
                //         $record->is_active == false ? $record->update(['event_status' => true]) : $record->update(['event_status' => false]);
                //     }
                // }
            })
        ];
    }


    public function render()
    {
        return view('livewire.admin.events');
    }
}
