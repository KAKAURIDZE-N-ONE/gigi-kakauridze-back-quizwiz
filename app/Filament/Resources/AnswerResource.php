<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnswerResource\Pages;
use App\Filament\Resources\AnswerResource\RelationManagers;
use App\Models\Answer;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('question_id')
                ->label('Question')
                ->relationship('question', 'title')
                ->required()
                ->searchable(),

            Forms\Components\Textarea::make('answer')
                ->label('Answer Text')
                ->required(),

            Forms\Components\Toggle::make('is_correct')
                ->label('Is Correct?')
                ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    Tables\Columns\TextColumn::make('id')
                        ->label('ID')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('question.question')
                        ->label('Question Title')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('answer')
                        ->label('Answer Text')
                        ->limit(50)
                        ->searchable(),

                    Tables\Columns\BooleanColumn::make('is_correct')
                        ->label('Correct'),

                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Created At')
                        ->dateTime()
                        ->sortable(),
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAnswers::route('/'),
            'create' => Pages\CreateAnswer::route('/create'),
            'edit' => Pages\EditAnswer::route('/{record}/edit'),
        ];
    }
}
