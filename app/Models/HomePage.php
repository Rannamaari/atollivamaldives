<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = [
        'name',
        'hero_image',
        'experience_image',
        'resorts_card_image',
        'guesthouses_card_image',
        'city_hotels_card_image',
        'liveaboards_card_image',
        'kicker',
        'arabic_kicker',
        'heading_line_one',
        'arabic_heading_line_one',
        'heading_line_two',
        'arabic_heading_line_two',
        'heading_emphasis',
        'arabic_heading_emphasis',
        'description',
        'arabic_description',
        'arabic_content',
        'explore_kicker',
        'explore_heading_line_one',
        'explore_heading_emphasis',
        'resorts_card_copy',
        'guesthouses_card_copy',
        'city_hotels_card_copy',
        'liveaboards_card_copy',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'arabic_content' => 'array',
        ];
    }

    public static function arabicContentDefaults(): array
    {
        return [
            'hero' => [
                'kicker' => 'المالديف كما تحلم بها، مخططة بعناية',
                'heading_line_one' => 'اعثر على طريقك',
                'heading_line_two' => 'إلى',
                'heading_emphasis' => 'الجنة.',
                'description' => 'منتجعات مختارة بعناية ورحلات بحرية مميزة وتجارب سفر في المالديف خُطّطت لتناسبك.',
            ],
            'finder' => [
                'eyebrow' => 'خطّط لإقامتك',
                'heading' => 'اعثر على إقامتك المثالية في المالديف',
                'description' => 'منتجعات وبيوت ضيافة ورحلات بحرية وإقامات مختارة بعناية في المالديف.',
                'tabs' => ['المنتجعات', 'بيوت الضيافة', 'رحلات القوارب', 'فنادق المدينة', 'الباقات'],
                'destination_label' => 'الوجهة أو مكان الإقامة',
                'destination_placeholder' => 'منتجع، جزيرة، جزيرة مرجانية، ماليه...',
                'check_in' => 'تاريخ الوصول',
                'check_out' => 'تاريخ المغادرة',
                'guests' => 'الضيوف',
                'adults' => 'البالغون',
                'adults_help' => '13 سنة فما فوق',
                'children' => 'الأطفال',
                'children_help' => 'من 0 إلى 12 سنة',
                'rooms' => 'الغرف',
                'rooms_help' => 'لمزيد من الراحة',
                'search' => 'ابحث',
            ],
            'explore' => [
                'kicker' => 'اكتشف المالديف',
                'heading_line_one' => 'تصفح',
                'heading_emphasis' => 'منتجات السفر لدينا.',
                'labels' => ['المنتجعات', 'بيوت الضيافة', 'فنادق المدينة', 'رحلات القوارب'],
                'copies' => [
                    'ملاذات جزر خاصة وفيلات فوق الماء وإقامات فاخرة مختارة بعناية.',
                    'إقامات في جزر محلية للمسافرين الباحثين عن الثقافة والقيمة والحياة الشاطئية.',
                    'إقامات مريحة في ماليه وبالقرب من المطار للتوقفات والزيارات القصيرة.',
                    'رحلات بحرية مصممة للغوص وركوب الأمواج والمواثيق الخاصة.',
                ],
            ],
            'intro' => [
                'kicker' => 'أكثر من مجرد عطلة',
                'heading_line_one' => 'المالديف،',
                'heading_emphasis' => 'بطابع شخصي.',
                'description' => 'من أول سؤال حتى رحلة الطائرة المائية الأخيرة، نجعل كل تفصيل سهلاً. يستخدم فريقنا المقيم في المالديف معرفته المحلية لصنع رحلة تبدو وكأنها خُطّطت لك وحدك.',
                'cta' => 'تحدث إلى خبير محلي',
            ],
            'benefits_heading' => 'لماذا تحجز مع أتوليفا المالديف؟',
            'benefits' => [
                ['title' => 'نحن في المالديف', 'description' => 'بصفتنا وكالة سفر محلية، تقدم أتوليفا المالديف معرفة مباشرة ودعماً موثوقاً طوال رحلتك. نحن في المالديف ويمكن الوصول إلينا بسهولة متى احتجت إلى المساعدة.'],
                ['title' => 'إقامات مختارة بعناية', 'description' => 'من المنتجعات الفاخرة وبيوت الضيافة الساحرة إلى رحلات القوارب التي لا تنسى، نختار خيارات الإقامة بعناية لتناسب تفضيلاتك وتوقعاتك وميزانيتك.'],
                ['title' => 'معرفة محلية وخدمة شخصية', 'description' => 'يفهم فريقنا المالديف ويقدم توصيات عملية وصادقة حول الإقامة والتنقلات والطعام والرحلات والغوص والسنوركلينغ وتجارب الجزر الأخرى.'],
                ['title' => 'أسعار تنافسية وعروض خاصة', 'description' => 'نعمل عن قرب مع شركائنا في السفر لنقدم لك أسعاراً جذابة وعروضاً موسمية ومزايا حصرية لتستمتع بأفضل قيمة لعطلتك في المالديف.'],
                ['title' => 'مساعدة في الرحلات الدولية', 'description' => 'يمكننا مساعدتك في العثور على رحلات دولية مناسبة ووصلات مريحة من وإلى المالديف، لتصبح رحلتك كاملة أسهل في التخطيط.'],
                ['title' => 'مساعدة في تأمين السفر', 'description' => 'احم رحلتك من النفقات الطبية غير المتوقعة وتعطل السفر وفقدان الأمتعة والوثائق والمواقف الطارئة الأخرى من خلال خيارات تأمين مناسبة.'],
                ['title' => 'رحلتك مخططة لك شخصياً', 'description' => 'كل مسافر مختلف. سواء كنت تخطط لشهر عسل أو عطلة عائلية أو مغامرة غوص أو رحلة فاخرة، تساعدك أتوليفا المالديف على تصميم تجربة تناسبك.'],
            ],
            'products' => [
                'kicker' => 'منتجات السفر',
                'heading_line_one' => 'منتجعات ورحلات',
                'heading_emphasis' => 'وإقامات في الجزر.',
                'description' => 'منتجعات وبيوت ضيافة وفنادق مدينة وباقات ورحلات بحرية مختارة لطابعها واهتمامها وإحساسها بالمكان.',
                'explore_cta' => 'اكتشف جميع المنتجات',
            ],
            'experience' => [
                'kicker' => 'ما وراء الأزرق',
                'heading_line_one' => 'تعال من أجل الجزر.',
                'heading_emphasis' => 'وتذكّر الشعور.',
                'description' => 'اسبح مع أسماك المانتا، وتناول الإفطار على شريط رملي هادئ، واكتشف إيقاع حياة الجزر من أهلها.',
                'cta' => 'اكتشف التجارب',
            ],
            'journal' => [
                'kicker' => 'المدونة',
                'heading_line_one' => 'حكايات من',
                'heading_emphasis' => 'الجزر.',
                'cta' => 'عرض كل المقالات',
                'read_cta' => 'اقرأ المقال',
            ],
            'inquiry' => [
                'kicker' => 'تخطيط سفر شخصي',
                'heading_line_one' => 'أخبرنا بما',
                'heading_emphasis' => 'تفكر به.',
                'description' => 'شاركنا بعض التفاصيل وسيساعدك فريقنا في تصميم رحلة المالديف المناسبة لك.',
                'name' => 'الاسم',
                'phone' => 'رقم واتساب',
                'nationality' => 'الجنسية',
                'country_placeholder' => 'اختر الدولة',
                'travel_type' => 'نوع الرحلة',
                'arrival' => 'تاريخ الوصول',
                'departure' => 'تاريخ المغادرة',
                'travellers' => 'المسافرون',
                'budget' => 'الميزانية التقريبية',
                'budget_placeholder' => 'مثال: 3,000 دولار أمريكي',
                'message' => 'أي تفاصيل أخرى؟',
                'message_placeholder' => 'أخبرنا عن رحلتك المثالية',
                'submit' => 'اطلب خطة رحلة',
            ],
            'closing' => [
                'kicker' => 'رحلتك تبدأ هنا',
                'heading_line_one' => 'لنصنع معاً شيئاً',
                'heading_emphasis' => 'لا ينسى.',
                'cta' => 'ابدأ التخطيط عبر واتساب',
            ],
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getHeroImageUrlAttribute(): string
    {
        return $this->hero_image
            ? $this->storageImageUrl($this->hero_image)
            : 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=2200&q=90';
    }

    public function getResortsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->resorts_card_image);
    }

    public function getExperienceImageUrlAttribute(): string
    {
        return $this->storageImageUrl($this->experience_image)
            ?: 'https://images.unsplash.com/photo-1544550285-f813152fb2fd?auto=format&fit=crop&w=1500&q=85';
    }

    public function getGuesthousesCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->guesthouses_card_image);
    }

    public function getCityHotelsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->city_hotels_card_image);
    }

    public function getLiveaboardsCardImageUrlAttribute(): ?string
    {
        return $this->storageImageUrl($this->liveaboards_card_image);
    }

    protected function storageImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
