<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Numeric;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $label = 'Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Menu'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->label('Harga')
                    ->Numeric(),
                Forms\Components\Toggle::make('has_spiciness_option')
                    ->label('Menu Memiliki Opsi Level Pedas?')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Reset spiciness_level jika toggle dimatikan
                        if (!$state) {
                            $set('spiciness_level', null);
                        }
                    }),

                Forms\Components\Select::make('spiciness_level')
                    ->label('Level Pedas Default')
                    ->options([
                        'original' => 'Original (Tidak Pedas)',
                        'mild' => 'Sedikit Pedas',
                        'medium' => 'Pedas Sedang',
                        'extra_pedas' => 'Extra Pedas'
                    ])
                    ->default('original')
                    ->visible(fn(callable $get) => $get('has_spiciness_option'))
                    ->required(fn(callable $get) => $get('has_spiciness_option')),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar')
                    ->directory('menu-images')
                    ->disk('public')
                    ->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori Menu')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Forms\Components\CheckboxList::make('toppings')
                    ->relationship('toppings', 'name')
                    ->columns(2)
                    ->label('Topping Menu')
                    ->helperText('Pilih topping jika menu ini bisa ditambahkan topping.'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('No'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Menu'),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->searchable()
                    ->label('Harga')
                    ->money('idr', true)
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
                Tables\Columns\IconColumn::make('has_spiciness_option')
                    ->label('Level Pedas?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('spiciness_level')
                    ->label('Level Default')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'original' => 'Original',
                            'mild' => 'Sedikit Pedas',
                            'medium' => 'Pedas Sedang',
                            'extra_pedas' => 'Extra Pedas',
                            default => '-'
                        };
                    }),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(50)

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
