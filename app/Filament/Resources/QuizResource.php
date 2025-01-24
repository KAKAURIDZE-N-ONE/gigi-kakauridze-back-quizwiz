<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Filament\Resources\QuizResource\RelationManagers;
use App\Models\Category;
use App\Models\Level;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->label('Title')
                ->required(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required(),

            Forms\Components\TextInput::make('duration')
                ->label('Duration (seconds)')
                ->numeric()
                ->required(),

                Forms\Components\Textarea::make('instructions')
                ->label('Instructions')
                ->required()
              ,
                Forms\Components\Select::make('level_id')
                ->label('Level')
                ->relationship('level', 'level')
                ->required(),

                MultiSelect::make('categories')
                ->label('Categories')
                ->preload()
                ->required()
                ->relationship('categories', 'name'),

                Forms\Components\FileUpload::make('image')
                ->label('Image')
                ->image()
                ->directory('images')
                ->required(),

                Repeater::make('questions')
                ->label('Questions')
                ->relationship('questions')
                ->schema([
                    Textarea::make('question')
                    ->label('Question')
                    ->required(),
                    TextInput::make('point')
                    ->label('Points')
                    ->required()
                    ->numeric()
                    ->maxLength(10),
                    Repeater::make('answers')
                    ->label('Answers')
                    ->relationship('answers')
                    ->schema([
                        TextArea::make('answer')
                            ->label('Answer Text')
                            ->required(),

                        Toggle::make('is_correct')
                            ->label('Is Correct?')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Add Answer'),

                ])

                ->columnSpan('full')
                ->createItemButtonLabel('Add Question')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('ID')
                ->sortable(),

            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->limit(50),

            Tables\Columns\TextColumn::make('duration')
                ->label('Duration (seconds)')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_filled')
                ->label('Total Filled')
                ->sortable(),
            Tables\Columns\TextColumn::make('level.level')
                    ->label('Level Name')
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
