<?php

namespace App\Http\Livewire\Admin;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Giveaway as GiveawayModel;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;

class Giveaway extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;
    public $event;

    protected function getTableQuery(): Builder
    {
        return GiveawayModel::query()->where('event_id', $this->event->id);
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('save')
            ->modalHeading('Add new')
            ->modalButton('Save')
            ->after(function () {

            })
            ->label('Add New')
            ->button()
            ->color('primary')
            ->icon('heroicon-o-plus')
            ->form([
                Forms\Components\TextInput::make('name')->label("Name")->required(),
            ])
            ->action(function ($data) {
                GiveawayModel::create([
                    'event_id' => $this->event->id,
                    'name' => $data['name'],
                ]);

                $this->dialog()->success(
                    $title = 'Success',
                    $description = 'Saved Successfully'
                );
            })
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
                    Forms\Components\TextInput::make('name')->label("Name")->required(),
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
                ->visible(fn ($record) => $record->attendances()->count() == 0),
                ])
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('event.event_name')
            ->label('Event')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('name')
            ->label('Name')->sortable()->searchable(),
        ];
    }

    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();
    }

    public function render()
    {
        return view('livewire.admin.giveaway');
    }
}
