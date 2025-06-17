<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $label = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name'),
                Tables\Columns\TextColumn::make('table_number')->label('Meja'),
                Tables\Columns\TextColumn::make('total')
                    ->money('idr', true)
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success', 'paid' => 'success',
                        'pending' => 'warning',
                        'challenge' => 'info',
                        'expired' => 'gray',
                        'failed', 'cancel' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Sudah Dibayar',
                        'pending' => 'Menunggu Pembayaran',
                        'challenge' => 'Challenge (Verifikasi)',
                        'expired' => 'Kadaluarsa',
                        'failed' => 'Gagal',
                        'cancel' => 'Dibatalkan',
                    ])
                    ->label('Status Pembayaran')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('printReceipt')
                    ->label('Cetak Struk')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Order $record) {
                        $pdf = Pdf::loadView('admin.receipts.order', [
                            'order' => $record->load(['items.menu']),
                        ]);
                        $pdf->setPaper([0, 0, 226.77, 1000], 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "struk-{$record->order_id}.pdf");
                    })
                    ->visible(fn(Order $record) => in_array($record->status, ['success', 'paid'])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('printReceipts')
                        ->label('Cetak Struk')
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $records = $records->filter(
                                fn($record) =>
                                in_array($record->status, ['success', 'paid'])
                            );

                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title('Tidak ada struk yang dapat dicetak')
                                    ->body('Pilih transaksi dengan status Sudah Dibayar')
                                    ->send();
                                return;
                            }
                            if ($records->count() === 1) {
                                $order = $records->first();
                                $pdf = Pdf::loadView('admin.receipts.order', [
                                    'order' => $order->load(['items.menu']),
                                ]);

                                $pdf->setPaper([0, 0, 226.77, 1000], 'portrait');

                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, "struk-{$order->order_id}.pdf");
                            }
                            $pdf = Pdf::loadView('admin.receipts.batch', [
                                'orders' => $records->load(['items.menu']),
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, "struk-batch-" . now()->format('Y-m-d-H-i-s') . ".pdf");
                        })
                        ->deselectRecordsAfterCompletion()
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
