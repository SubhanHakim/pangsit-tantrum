<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToppingResource\Pages;
use App\Filament\Resources\ToppingResource\RelationManagers;
use App\Models\Topping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ToppingResource extends Resource
{
    protected static ?string $model = Topping::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

     protected static ?string $navigationGroup = 'Menu';
    protected static ?string $navigationLabel = 'Topping';
    protected static ?string $label = 'Topping';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Topping Name'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->label('Harga Topping')
                    ->Numeric(),
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
                    ->label('Topping Name'),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->searchable()
                    ->money('IDR')
                    ->label('Harga Topping'),
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
            'index' => Pages\ListToppings::route('/'),
            'create' => Pages\CreateTopping::route('/create'),
            'edit' => Pages\EditTopping::route('/{record}/edit'),
        ];
    }
}
