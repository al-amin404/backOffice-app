<?php

namespace App\Filament\Resources\AgentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Passport;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Squire\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PassportResource;
use App\Filament\Resources\PassportResource\Pages\EditPassport;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PassportsRelationManager extends RelationManager
{
    protected static string $relationship = 'passports';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->maxDate(now())
                            ->native(false)
                            ->closeOnDateSelection(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),
                    
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
                        Forms\Components\Textarea::make('note'),
                    ])
                    ->columns(2)
                    ->columnSpan(1),

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
                    ->columnSpan(1),
    
                    Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('passport_photo')
                        ->image()
                        ->openable()
                        ->directory('passports'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('SL.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->wrap(true),
                Tables\Columns\TextColumn::make('passport')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.title')
                    ->searchable()
                    ->sortable()
                    ->wrap(true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reference')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agent.name')
                    ->searchable()
                    ->wrap(true)
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(function($record) {
                return EditPassport::getUrl([$record->id]);
            })
            ->defaultSort('id', 'desc');
    }
}
