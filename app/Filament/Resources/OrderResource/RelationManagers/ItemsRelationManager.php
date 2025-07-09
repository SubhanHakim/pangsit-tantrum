<?php
// app/Filament/Resources/OrderResource/RelationManagers/ItemsRelationManager.php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('menu_id')
                    ->label('Menu')
                    ->relationship('menu', 'name')
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                Forms\Components\Textarea::make('note')
                    ->label('Catatan')
                    ->rows(3),

                Forms\Components\Select::make('spiciness_level')
                    ->label('Level Pedas')
                    ->options([
                        'original' => 'Original (Tidak Pedas)',
                        'mild' => 'Sedikit Pedas',
                        'medium' => 'Pedas Sedang',
                        'extra_pedas' => 'Extra Pedas',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('menu.name')
                    ->label('Menu'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),

                Tables\Columns\TextColumn::make('spiciness_level')
                    ->label('Level Pedas')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'original' => 'Original',
                        'mild' => 'Sedikit Pedas',
                        'medium' => 'Pedas Sedang',
                        'extra_pedas' => 'Extra Pedas',
                        default => '-'
                    }),

                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}