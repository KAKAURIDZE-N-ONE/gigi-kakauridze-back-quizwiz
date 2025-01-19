<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('quiz_id')
                    ->label('Quiz')
                    ->relationship('quiz', 'title') // Assuming `Quiz` model has a `title` field
                    ->required()
                    ->searchable(),

                Forms\Components\Textarea::make('question')
                    ->label('Question Text')
                    ->required(),

                Forms\Components\TextInput::make('point')
                    ->label('Points')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Repeater::make('answers')
                    ->label('Answers')
                    ->relationship('answers')  // Bind it to the answers relationship
                    ->schema([
                        TextArea::make('answer')
                            ->label('Answer Text')
                            ->required(),

                        Toggle::make('is_correct')
                            ->label('Is Correct?')
                            ->default(false),
                    ])
                    ->columnSpan('full')
                    ->createItemButtonLabel('Add Answer'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quiz.title')
                    ->label('Quiz Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Question Text')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('point')
                    ->label('Points')
                    ->sortable(),

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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
