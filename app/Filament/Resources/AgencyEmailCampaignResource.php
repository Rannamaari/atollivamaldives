<?php

namespace App\Filament\Resources;

use App\Enums\AgencyEmailCampaignStatus;
use App\Filament\Resources\AgencyEmailCampaignResource\Pages;
use App\Filament\Resources\AgencyEmailCampaignResource\RelationManagers\RecipientsRelationManager;
use App\Models\AgencyContact;
use App\Models\AgencyEmailCampaign;
use App\Models\AgencyPartner;
use App\Models\EmailTemplate;
use App\Models\PartnerCollection;
use App\Services\OperationsHub\AgencyEmailCampaignService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgencyEmailCampaignResource extends Resource
{
    protected static ?string $model = AgencyEmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Operations Hub';

    protected static ?string $navigationLabel = 'Agency Email Campaigns';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Campaign setup')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options(AgencyEmailCampaignStatus::options())
                        ->default(AgencyEmailCampaignStatus::Draft->value)
                        ->disabled(),
                    Forms\Components\Select::make('email_template_id')
                        ->label('Email template')
                        ->relationship('emailTemplate', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->default(today())
                        ->required(),
                    Forms\Components\TimePicker::make('send_window_starts_at')
                        ->seconds(false)
                        ->default(config('operations_hub.campaigns.default_send_time', '09:00'))
                        ->required(),
                    Forms\Components\TextInput::make('daily_limit')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue((int) config('operations_hub.campaigns.hard_daily_limit', 20))
                        ->default((int) config('operations_hub.campaigns.default_daily_limit', 10))
                        ->required()
                        ->helperText('Hard safety cap: no more than '.config('operations_hub.campaigns.hard_daily_limit', 20).' emails per day.'),
                    Forms\Components\TextInput::make('interval_minutes')
                        ->numeric()
                        ->minValue((int) config('operations_hub.campaigns.minimum_interval_minutes', 5))
                        ->default((int) config('operations_hub.campaigns.default_interval_minutes', 5))
                        ->required()
                        ->helperText('Emails are spaced out automatically. Minimum '.config('operations_hub.campaigns.minimum_interval_minutes', 5).' minutes.'),
                    Forms\Components\Select::make('partner_collection_ids')
                        ->label('Folders / lists')
                        ->multiple()
                        ->options(fn () => PartnerCollection::query()->whereIn('scope', ['agency_partners', 'both'])->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText('Pick one or more folders like GCC agencies or priority partners.'),
                    Forms\Components\Select::make('agency_partner_ids')
                        ->label('Agency partners')
                        ->multiple()
                        ->options(fn () => AgencyPartner::query()->where('is_active', true)->orderByRaw("coalesce(nullif(trading_name, ''), legal_company_name)")->get()->mapWithKeys(fn (AgencyPartner $agency) => [$agency->id => $agency->trading_name ?: $agency->legal_company_name]))
                        ->searchable()
                        ->preload()
                        ->helperText('You can choose individual agencies, folders, or both.'),
                    Forms\Components\Select::make('agency_contact_ids')
                        ->label('Individual contacts')
                        ->multiple()
                        ->options(fn () => AgencyContact::query()
                            ->with('agencyPartner')
                            ->where('is_active', true)
                            ->whereNotNull('email')
                            ->where('email', '!=', '')
                            ->orderBy('full_name')
                            ->get()
                            ->mapWithKeys(fn (AgencyContact $contact) => [
                                $contact->id => trim($contact->full_name.' - '.($contact->agencyPartner?->trading_name ?: $contact->agencyPartner?->legal_company_name).' <'.$contact->email.'>')
                            ]))
                        ->searchable()
                        ->preload()
                        ->helperText('Search and select specific people instead of entire agencies when needed.')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('manual_recipients')
                        ->label('Manual recipients')
                        ->rows(5)
                        ->placeholder("Jane Smith <jane@example.com>\nreservations@example.com\nJohn Doe <john@agency.com>")
                        ->helperText('One recipient per line. You can paste just an email, or Name <email>. These recipients are included alongside selected agencies or contacts.')
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Sending details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('sender_name')
                        ->default(config('operations_hub.company.name')),
                    Forms\Components\TextInput::make('sender_email')
                        ->email()
                        ->default(config('operations_hub.company.email')),
                    Forms\Components\TextInput::make('reply_to_email')
                        ->email(),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('subject_override')
                        ->columnSpanFull()
                        ->helperText('Optional. Leave empty to use the selected email template subject.'),
                    Forms\Components\Textarea::make('body_override')
                        ->rows(10)
                        ->columnSpanFull()
                        ->helperText('Optional. Leave empty to use the selected email template body.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? AgencyEmailCampaignStatus::tryFrom((string) $state)?->label() ?? $state),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('daily_limit')->label('Per day'),
                Tables\Columns\TextColumn::make('interval_minutes')->label('Gap (mins)'),
                Tables\Columns\TextColumn::make('recipients_count')
                    ->counts('recipients')
                    ->label('Recipients'),
                Tables\Columns\TextColumn::make('sent_count')
                    ->state(fn (AgencyEmailCampaign $record) => $record->recipients()->where('status', 'sent')->count())
                    ->label('Sent'),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(AgencyEmailCampaignStatus::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->visible(fn (AgencyEmailCampaign $record) => in_array($record->status, [AgencyEmailCampaignStatus::Draft, AgencyEmailCampaignStatus::Paused], true))
                    ->requiresConfirmation()
                    ->action(function (AgencyEmailCampaign $record, AgencyEmailCampaignService $service): void {
                        $service->startCampaign($record);

                        Notification::make()->success()->title('Campaign started')->body('Recipients have been scheduled with the daily cap and interval rules.')->send();
                    }),
                Tables\Actions\Action::make('pause')
                    ->label('Pause')
                    ->icon('heroicon-o-pause')
                    ->visible(fn (AgencyEmailCampaign $record) => in_array($record->status, [AgencyEmailCampaignStatus::Scheduled, AgencyEmailCampaignStatus::Sending], true))
                    ->requiresConfirmation()
                    ->action(function (AgencyEmailCampaign $record, AgencyEmailCampaignService $service): void {
                        $service->pauseCampaign($record);

                        Notification::make()->success()->title('Campaign paused')->send();
                    }),
                Tables\Actions\Action::make('stop')
                    ->label('Stop')
                    ->color('danger')
                    ->icon('heroicon-o-stop')
                    ->visible(fn (AgencyEmailCampaign $record) => ! in_array($record->status, [AgencyEmailCampaignStatus::Completed, AgencyEmailCampaignStatus::Stopped], true))
                    ->requiresConfirmation()
                    ->action(function (AgencyEmailCampaign $record, AgencyEmailCampaignService $service): void {
                        $service->stopCampaign($record);

                        Notification::make()->success()->title('Campaign stopped')->body('Unsent recipients were cancelled.')->send();
                    }),
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
            RecipientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencyEmailCampaigns::route('/'),
            'create' => Pages\CreateAgencyEmailCampaign::route('/create'),
            'edit' => Pages\EditAgencyEmailCampaign::route('/{record}/edit'),
        ];
    }
}
