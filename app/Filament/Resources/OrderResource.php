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

    protected static ?string $pluralLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('table_number')
                            ->label('Nomor Meja')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2),
                Forms\Components\Select::make('order_type')
                    ->label('Tipe Pesanan')
                    ->options([
                        'dine_in' => 'Makan di Tempat',
                        'takeaway' => 'Dibawa Pulang',
                    ])
                    ->default('dine_in')
                    ->required()
                    ->reactive(),

                Forms\Components\Section::make('Status Transaksi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'paid' => 'Sudah Dibayar',
                                'success' => 'Berhasil',
                                'challenge' => 'Verifikasi',
                                'expired' => 'Kadaluarsa',
                                'failed' => 'Gagal',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Select::make('food_status')
                            ->label('Status Makanan')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'preparing' => 'Sedang Dipersiapkan',
                                'ready' => 'Siap Disajikan',
                                'completed' => 'Selesai',
                            ])
                            ->default('pending')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $now = now();
                                match ($state) {
                                    'preparing' => $set('preparing_at', $now),
                                    'ready' => $set('ready_at', $now),
                                    'completed' => $set('completed_at', $now),
                                    default => null
                                };
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Timestamp Status Makanan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('preparing_at')
                            ->label('Waktu Mulai Dipersiapkan')
                            ->visible(fn(callable $get) => in_array($get('food_status'), ['preparing', 'ready', 'completed']))
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('ready_at')
                            ->label('Waktu Siap')
                            ->visible(fn(callable $get) => in_array($get('food_status'), ['ready', 'completed']))
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Waktu Selesai')
                            ->visible(fn(callable $get) => $get('food_status') === 'completed')
                            ->seconds(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Total')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('table_number')
                    ->label('Meja')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('idr', true)
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Menunggu',
                        'paid' => 'Dibayar',
                        'success' => 'Berhasil',
                        'challenge' => 'Verifikasi',
                        'expired' => 'Kadaluarsa',
                        'failed' => 'Gagal',
                        'cancelled' => 'Dibatalkan',
                        default => $state
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => ['paid', 'success'],
                        'info' => 'challenge',
                        'gray' => 'expired',
                        'danger' => ['failed', 'cancelled'],
                    ]),
                Tables\Columns\BadgeColumn::make('order_type')
                    ->label('Tipe Pesanan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'dine_in' => '🍽️ Makan di Tempat',
                        'takeaway' => '🥡 Dibawa Pulang',
                        default => $state
                    })
                    ->colors([
                        'primary' => 'dine_in',
                        'success' => 'takeaway',
                    ]),

                Tables\Columns\BadgeColumn::make('food_status')
                    ->label('Status Makanan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => '⏳ Menunggu',
                        'preparing' => '👨‍🍳 Dipersiapkan',
                        'ready' => '🔔 Siap',
                        'completed' => '✅ Selesai',
                        default => $state
                    })
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'preparing',
                        'info' => 'ready',
                        'success' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('preparing_at')
                    ->label('Mulai Masak')
                    ->dateTime('H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ready_at')
                    ->label('Siap')
                    ->dateTime('H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai')
                    ->dateTime('H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Menunggu Pembayaran',
                        'paid' => 'Sudah Dibayar',
                        'success' => 'Berhasil',
                        'challenge' => 'Verifikasi',
                        'expired' => 'Kadaluarsa',
                        'failed' => 'Gagal',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\SelectFilter::make('food_status')
                    ->label('Status Makanan')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'preparing' => 'Sedang Dipersiapkan',
                        'ready' => 'Siap Disajikan',
                        'completed' => 'Selesai',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),

                Tables\Filters\Filter::make('paid_orders')
                    ->label('Sudah Dibayar')
                    ->query(fn(Builder $query): Builder => $query->whereIn('status', ['paid', 'success'])),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('updateFoodStatus')
                        ->label('Update Status Makanan')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('food_status')
                                ->label('Status Makanan')
                                ->options([
                                    'pending' => 'Menunggu Konfirmasi',
                                    'preparing' => 'Sedang Dipersiapkan',
                                    'ready' => 'Siap Disajikan',
                                    'completed' => 'Selesai',
                                ])
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data) {
                            $status = $data['food_status'];
                            $now = now();

                            $updateData = ['food_status' => $status];

                            switch ($status) {
                                case 'preparing':
                                    $updateData['preparing_at'] = $now;
                                    break;
                                case 'ready':
                                    $updateData['ready_at'] = $now;
                                    break;
                                case 'completed':
                                    $updateData['completed_at'] = $now;
                                    break;
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->success()
                                ->title('Status berhasil diupdate')
                                ->body("Order #{$record->order_id} status makanan diupdate ke: {$record->food_status_label}")
                                ->send();
                        })
                        ->visible(fn(Order $record) => !$record->isCompleted()),

                    Tables\Actions\Action::make('markAsPreparing')
                        ->label('Mulai Masak')
                        ->icon('heroicon-o-fire')
                        ->color('primary')
                        ->action(function (Order $record) {
                            $record->markAsPreparing();

                            Notification::make()
                                ->success()
                                ->title('Status diupdate')
                                ->body("Order #{$record->order_id} sedang dipersiapkan")
                                ->send();
                        })
                        ->visible(fn(Order $record) => $record->isPending())
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('markAsReady')
                        ->label('Siap Disajikan')
                        ->icon('heroicon-o-bell')
                        ->color('info')
                        ->action(function (Order $record) {
                            $record->markAsReady();

                            Notification::make()
                                ->success()
                                ->title('Makanan siap!')
                                ->body("Order #{$record->order_id} siap disajikan")
                                ->send();
                        })
                        ->visible(fn(Order $record) => $record->isPreparing())
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('markAsCompleted')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Order $record) {
                            $record->markAsCompleted();

                            Notification::make()
                                ->success()
                                ->title('Order selesai!')
                                ->body("Order #{$record->order_id} telah selesai")
                                ->send();
                        })
                        ->visible(fn(Order $record) => $record->isReady())
                        ->requiresConfirmation(),

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
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulkUpdateFoodStatus')
                        ->label('Update Status Makanan')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('food_status')
                                ->label('Status Makanan')
                                ->options([
                                    'pending' => 'Menunggu Konfirmasi',
                                    'preparing' => 'Sedang Dipersiapkan',
                                    'ready' => 'Siap Disajikan',
                                    'completed' => 'Selesai',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $status = $data['food_status'];
                            $now = now();
                            $count = 0;

                            foreach ($records as $record) {
                                $updateData = ['food_status' => $status];

                                switch ($status) {
                                    case 'preparing':
                                        $updateData['preparing_at'] = $now;
                                        break;
                                    case 'ready':
                                        $updateData['ready_at'] = $now;
                                        break;
                                    case 'completed':
                                        $updateData['completed_at'] = $now;
                                        break;
                                }

                                $record->update($updateData);
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title('Status berhasil diupdate')
                                ->body("{$count} order berhasil diupdate statusnya")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('printReceipts')
                        ->label('Cetak Struk')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function ($records) {
                            $records = $records->filter(
                                fn($record) => in_array($record->status, ['success', 'paid'])
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // OrderResource\Widgets\OrderStatsOverview::class,
            // OrderResource\Widgets\OrderChart::class,
        ];
    }

    // Custom queries untuk statistics
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items.menu'])
            ->withCount('items');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('food_status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('food_status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }
}
