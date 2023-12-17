<?php

namespace App\Http\Livewire\Admin;

use Filament\Forms;
use Filament\Tables;
use App\Models\Printer as ModelsPrinter;
use Livewire\Component;
use WireUi\Traits\Actions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Filament\Notifications\Notification;

class Printers extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Actions;
    public $event;

    protected function getTableQuery(): Builder
    {
        return ModelsPrinter::query();
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
                Forms\Components\Select::make('user_id')
                ->label("User")
                ->options(\App\Models\User::whereDoesntHave('printer')->pluck('name', 'id')->toArray())
                ->required(),
                Forms\Components\TextInput::make('ip_address')->label("IP Address")->required(),
            ])
            ->requiresConfirmation()
        ];
    }

    protected function getTableActions()
    {
        return [
                EditAction::make('edit')
                ->icon('heroicon-o-pencil')
                ->label('Edit IP Address')
                ->color('success')
                ->button()
                ->outlined()
                ->after(function () {
                    $this->dialog()->success(
                        $title = 'Success',
                        $description = 'Updated Successfully'
                    );
                })
                ->form([
                    Forms\Components\TextInput::make('ip_address')->label("IP Address")->required(),
                ])
                ->requiresConfirmation(),
                Action::make('test_printer')
                ->icon('heroicon-o-printer')
                ->label('Test Printer')
                ->color('primary')
                ->button()
                ->action(function ($record) {
                    $this->testPrinter($record);
                })
        ];
    }

    public function testPrinter($record)
    {
     $records = ModelsPrinter::find($record);
     $active_event = \App\Models\Event::where('event_status', 1)->first();
     try{
        $printerIp = $records->first()->ip_address;
        $printerPort = 9100;
        $content = 'Printer is Good!';
        $connector = new NetworkPrintConnector($printerIp);
        $printer = new Printer($connector);
        if($printer)
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer -> text(strtoupper($active_event->event_name)."\n");
        $printer->setEmphasis(false);
        $printer -> text(\Carbon\Carbon::parse($active_event->date_of_event)->format('F d, Y')."\n");
        $printer -> feed(2);
        $printer -> text(strtoupper($records->first()->user->name)."\n");
        $printer -> feed(4);
        $printer -> text($content);
        $printer -> feed(4);
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
            Tables\Columns\TextColumn::make('user.name')
            ->label('Name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('ip_address')
            ->label('IP Address')->sortable()->searchable(),
        ];
    }

    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();

    }



    public function render()
    {
        return view('livewire.admin.printers');
    }
}
