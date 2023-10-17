<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Filament\Forms;
use Filament\Tables;
use App\Models\Members as MembersModel;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;

class Members extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;

    protected function getTableQuery(): Builder
    {
        return MembersModel::query();
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
            ])
            ->requiresConfirmation(),
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

    public function render()
    {
        return view('livewire.admin.members');
    }
}
