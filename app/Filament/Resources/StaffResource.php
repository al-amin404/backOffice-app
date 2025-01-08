<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('staff_id')
                    ->prefix('LT')
                    ->required()
                    ->unique(Staff::class, 'staff_id', ignoreRecord: true)
                    ->maxLength(36),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(Staff::class, 'email', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile')
                    ->required()
                    ->tel()
                    ->maxLength(11),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required()
                    ->maxDate(now())
                    ->native(false)
                    ->closeOnDateSelection(),
                Forms\Components\TextInput::make('designation')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('department')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('joining_date')
                    ->required()
                    ->maxDate(now())
                    ->closeOnDateSelection(),
                Forms\Components\Select::make('blood_group')
                    ->label('Blood Group')
                    ->options([
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'O+' => 'O+',
                        'O-' => 'O-',
                    ])
                    ->native(false),
                Forms\Components\TextInput::make('nid')
                    ->label('NID')
                    ->numeric()
                    ->maxLength(13),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('photo')
                    ->avatar()
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(2048)
                    ->directory('staff'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff_id')
                    ->label('Staff ID')
                    ->prefix('LT-'),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile'),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn (Staff $record) => $record->name),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
