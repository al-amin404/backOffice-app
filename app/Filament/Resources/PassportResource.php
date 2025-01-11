<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Passport;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Squire\Models\Country;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\PassportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PassportResource\RelationManagers;
use App\Models\Agent;

class PassportResource extends Resource
{
    protected static ?string $model = Passport::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->schema(static::getPassportEntryFormSchema())
                ->columnSpan(['lg' => fn (?Passport $record) => $record === null ? 2 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Passport $passport): ?string => $passport->created_at?->format('d M Y H:i:s') . ' by ' . $passport->createdBy->name),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Updated at')
                            ->content(fn (Passport $passport): ?string => $passport->updated_at?->format('d M Y H:i:s') . ' by ' . $passport->updatedBy->name),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Passport $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('SL.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    //->prefix(fn(Passport $record) => $record->id . '. ')
                    ->wrap(true),
                Tables\Columns\TextColumn::make('passport')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.title')
                    ->searchable()
                    ->sortable()
                    ->wrap(true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('reference')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agent.name')
                    ->searchable(isIndividual: true)
                    ->wrap(true)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rlNumber')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->label('Updated by')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make()
                //     ->modalHeading(fn (Passport $record) => $record->name),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->tooltip('Actions')
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListPassports::route('/'),
            'create' => Pages\CreatePassport::route('/create'),
            'edit' => Pages\EditPassport::route('/{record}/edit'),
        ];
    }

    public static function getPassportEntryFormSchema(): array
    {
        return [
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Placeholder::make('id')
                    ->label(false)
                    ->content(fn (Passport $passport): ?string => 'SL- ' . $passport->id)
                    ->hidden(fn (?Passport $record) => $record === null)
                    ->disabled(fn (?Passport $record) => $record !== null)
                    ->columnSpanFull(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->autocapitalize('words')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('passport')
                        ->label('Passport No.')
                        ->required()
                        ->autocapitalize('characters')
                        ->maxLength(9),
                    Forms\Components\TextInput::make('mobile')
                        ->required()
                        ->tel()
                        ->maxLength(11),
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->required()
                        ->maxDate(now()),
                ])
                ->columns(2)
                ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->required()
                            ->relationship('service', 'title')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('country')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $query) => Country::where('name', 'like', "%{$query}%")->pluck('name', 'name'))
                            ->getOptionLabelUsing(fn ($value): ?string => Country::firstWhere('id', $value)?->getAttribute('name')),
                        Forms\Components\TextInput::make('reference')
                            ->hiddenOn('create')
                            ->disabled()
                            ->maxLength(255),
                        Forms\Components\Select::make('agent_id')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->required()
                            ->createOptionForm(static::getAgentFormSchema())
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Create Agent')
                                    ->modalSubmitActionLabel('Create agent')
                                    ->modalWidth('lg');
                        }),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'Received' => 'Received',
                                'On-hold' => 'On-hold',
                                'Processing' => 'Processing',
                                'Process Done' => 'Process Done',
                                'Medical Done' => 'Medical Done',
                                'GCC Fit' => 'GCC Fit',
                                'Mofa Done' => 'Mofa Done',
                                'Fingerprint Done' => 'Fingerprint Done',
                                'Sent for Embassy' => 'Sent for Embassy',
                                'Visa Issued' => 'Visa Issued',
                                'BMET Processing' => 'BMET Processing',
                                'Delivered' => 'Delivered',
                                'Failed' => 'Failed',
                                'Failed & Returned' => 'Failed & Returned',
                                
                            ])
                            ->native(false)
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Private Notes'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                    Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('visaNumber')
                            ->label('Visa Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('idNumber')
                            ->label('ID Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rlNumber')
                            ->label('RL No.')
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 1]),
                ])
                ->columns(3),

                Forms\Components\Section::make('Image')
                ->schema([
                    Forms\Components\FileUpload::make('passport_photo')
                    ->image()
                    ->openable()
                    ->directory('passports'),
                ])
                ->columns(2)
                ->columnSpan(['lg' => 2]),
        ];
    }

    public static function getAgentFormSchema(): array
    {
        return [
            
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email address')
                ->email()
                ->unique('agents', 'email')
                ->unique(),

            Forms\Components\TextInput::make('mobile')
                ->tel()
                ->required()
                ->maxLength(15),

            Forms\Components\DatePicker::make('date_of_birth')
                ->format('d/m/Y')
                ->maxDate(now()),

            Forms\Components\TextInput::make('nid')
                ->label('NID')
                ->numeric()
                ->maxLength(17),

            Forms\Components\Textarea::make('address'),
            Forms\Components\FileUpload::make('photo')
                ->avatar()
                ->directory('agents'),
    
        ];
    }
}
