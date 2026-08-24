<?php
namespace App\Filament\Resources;
use App\Enums\AccommodationType;
use App\Filament\Resources\AccommodationResource\Pages;
use App\Models\Accommodation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AccommodationResource extends Resource
{
    protected static ?string $model=Accommodation::class;
    protected static ?string $navigationIcon='heroicon-o-building-office-2';
    protected static ?string $navigationGroup='Travel Products';
    public static function form(Form $form):Form { return $form->schema([
        Forms\Components\Section::make('Property details')->columns(2)->schema([
            Forms\Components\Select::make('type')->options(collect(AccommodationType::cases())->mapWithKeys(fn($x)=>[$x->value=>$x->label()]))->required(),
            Forms\Components\TextInput::make('name')->required()->live(onBlur:true)->afterStateUpdated(fn($state,Forms\Set $set)=>$set('slug',Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord:true), Forms\Components\TextInput::make('tagline'),
            Forms\Components\Textarea::make('summary')->columnSpanFull(), Forms\Components\RichEditor::make('description')->columnSpanFull(),
            Forms\Components\TextInput::make('island'), Forms\Components\TextInput::make('atoll'), Forms\Components\Textarea::make('address')->columnSpanFull(),
        ]),
        Forms\Components\Section::make('Pricing & media')->columns(3)->schema([
            Forms\Components\TextInput::make('price_from')->numeric()->prefix('$'), Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3), Forms\Components\Select::make('price_unit')->options(['night'=>'Per night','trip'=>'Per trip','person'=>'Per person']),
            Forms\Components\FileUpload::make('images')->image()->multiple()->reorderable()->directory('accommodations')->disk('public')->columnSpanFull(),
            Forms\Components\TagsInput::make('amenities')->columnSpanFull(), Forms\Components\TextInput::make('rating')->numeric()->minValue(0)->maxValue(5),
        ]),
        Forms\Components\Section::make('Publishing')->columns(3)->schema([Forms\Components\Toggle::make('published'),Forms\Components\Toggle::make('featured'),Forms\Components\TextInput::make('sort_order')->numeric()->default(0)]),
        Forms\Components\Section::make('SEO')->collapsed()->schema([Forms\Components\TextInput::make('seo_title'),Forms\Components\Textarea::make('seo_description')]),
    ]); }
    public static function table(Table $table):Table { return $table->columns([Tables\Columns\ImageColumn::make('images')->circular()->stacked(),Tables\Columns\TextColumn::make('name')->searchable()->sortable(),Tables\Columns\TextColumn::make('type')->badge(),Tables\Columns\TextColumn::make('atoll')->searchable(),Tables\Columns\TextColumn::make('price_from')->money(fn($record)=>$record->currency),Tables\Columns\IconColumn::make('featured')->boolean(),Tables\Columns\IconColumn::make('published')->boolean()])->filters([Tables\Filters\SelectFilter::make('type')->options(['resort'=>'Resort','guesthouse'=>'Guesthouse','liveaboard'=>'Liveaboard']),Tables\Filters\TernaryFilter::make('published')])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]); }
    public static function getPages():array { return ['index'=>Pages\ListAccommodations::route('/'),'create'=>Pages\CreateAccommodation::route('/create'),'edit'=>Pages\EditAccommodation::route('/{record}/edit')]; }
}
