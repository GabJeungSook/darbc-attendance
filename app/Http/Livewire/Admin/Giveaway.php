<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Filament\Forms;
use Filament\Tables;
use App\Models\Giveaway as GiveawayModel;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;

class Giveaway extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;

    protected function getTableQuery(): Builder
    {
        return GiveawayModel::query();
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
                Forms\Components\TextInput::make('name')->label("Name")->required(),
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
            Tables\Columns\TextColumn::make('name')
            ->label('Name')->sortable()->searchable(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.giveaway');
    }
}
