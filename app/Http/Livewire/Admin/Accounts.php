<?php

namespace App\Http\Livewire\Admin;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;

class Accounts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;

    protected function getTableQuery(): Builder
    {
        return User::query()->where('role_id', '!=', 1);
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('save')
            ->modalHeading('Add Account')
            ->modalButton('Save')
            ->label('Add New')
            ->button()
            ->color('primary')
            ->icon('heroicon-o-plus')
            ->form([
                Forms\Components\TextInput::make('name')->label("Name")->required(),
                Forms\Components\TextInput::make('username')->label("Username")->required(),
                Forms\Components\TextInput::make('password')->label("Password")->required()->password(),
                Forms\Components\TextInput::make('confirm_password')->label("Confirm Password")->required()->password(),
            ])
            ->action(function ($data) {
                if($data['password'] != $data['confirm_password'])
                {
                    $this->dialog()->error(
                        $title = 'Oops!',
                        $description = 'Password and Confirm Password does not match'
                    );
                }else
                {
                    User::create([
                        'name' => $data['name'],
                        'username' => $data['username'],
                        'password' => bcrypt($data['password']),
                        'role_id' => 2
                    ]);
                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Saved Successfully'
                    );
                }
            })
           ->requiresConfirmation()
        ];
    }

    protected function getTableActions()
    {
        return [
          EditAction::make('edit')
            ->icon('heroicon-o-pencil')
            ->label('Update')
            ->color('success')
            ->button()
            ->outlined()
            ->form([
                Forms\Components\TextInput::make('name')->label("Name")->required(),
                Forms\Components\TextInput::make('username')->label("Username")->required(),
            ])
            ->requiresConfirmation(),
            Action::make('change_password')
            ->modalHeading('Change Password')
            ->modalButton('Save')
            ->label('Change Password')
            ->button()
            ->outlined()
            ->color('primary')
            ->icon('heroicon-o-key')
            ->form([
                Forms\Components\TextInput::make('password')->label("Password")->required()->password(),
                Forms\Components\TextInput::make('confirm_password')->label("Confirm Password")->required()->password(),
            ])
            ->action(function ($data, $record) {
                if($data['password'] != $data['confirm_password'])
                {
                    $this->dialog()->error(
                        $title = 'Oops!',
                        $description = 'Password and Confirm Password does not match'
                    );
                }else
                {
                    User::where('id', $record->id)->update([
                        'password' => bcrypt($data['password']),
                    ]);
                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Saved Successfully'
                    );
                }
            })
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
            ->label('Name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('username')
        ];
    }

    public function render()
    {
        return view('livewire.admin.accounts');
    }
}
