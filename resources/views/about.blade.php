@extends('layouts.app')

@section('title', $isArabic ? 'عن أتوليفا المالديف | عطلتك في المالديف مخططة بعناية' : 'About Atolliva Maldives | Your Maldives, Thoughtfully Planned')
@section('description', $isArabic ? 'تعرف على أتوليفا المالديف وخدماتنا في تخطيط العطلات الفاخرة والاقتصادية والملائمة لاحتياجات المسافرين المسلمين في المالديف.' : 'Learn the story behind Atolliva Maldives, our Maldives-based approach to travel planning, and where our office is located.')

@section('content')
@include('partials.site-nav', ['whatsAppText' => $isArabic ? 'مرحباً أتوليفا المالديف، أود معرفة المزيد عن خدمات تخطيط السفر لديكم.' : 'Hello Atolliva Maldives, I would like to learn more about your travel planning services.'])

<main class="about-page">
    <section class="about-hero">
        <div class="about-hero__backdrop"></div>
        <div class="about-hero__inner">
            <p class="about-hero__kicker">{{ $isArabic ? 'عن أتوليفا المالديف' : 'ABOUT ATOLLIVA MALDIVES' }}</p>
            <h1>{!! $isArabic ? 'المالديف كما<br><em>تحلم بها.</em>' : 'Your Maldives,<br><em>thoughtfully planned.</em>' !!}</h1>
            <p class="about-hero__intro">{{ $isArabic ? 'شركة سفر متخصصة في المالديف، نبني كل رحلة على إرشاد شخصي ومعرفة محلية موثوقة وطريقة أكثر سهولة واهتماماً لتخطيط الإقامات ورحلات القوارب والتجارب.' : 'A Maldives travel company built around personal guidance, trusted local perspective, and a more meaningful way to plan island stays, liveaboards, and holiday experiences.' }}</p>
        </div>
    </section>

    <section class="about-story">
        <div class="about-story__inner">
            <div class="about-story__heading">
                <p class="kicker">{{ $isArabic ? 'قصتنا' : 'OUR STORY' }}</p>
                <h2>{{ $isArabic ? 'أنشأنا أتوليفا لتجعل المالديف تجربة أكثر قرباً ووضوحاً وموثوقية.' : 'Created to make the Maldives feel more personal, thoughtful, and reliable.' }}</h2>
            </div>

            <div class="about-story__content">
                @if($isArabic)
                    <p>بدأت أتوليفا المالديف بفكرة بسيطة: مساعدة المسافرين على عيش تجربة المالديف بطريقة شخصية ومدروسة وموثوقة.</p>
                    <p>اسم <strong>أتوليفا</strong> مستوحى من أجمل ما يميز المالديف: <strong>الجزر المرجانية</strong>. فـ <strong>Atoll</strong> يرمز إلى الجزر والبحيرات والشعاب والمجتمعات المنتشرة في البلاد، بينما تضيف <strong>Viva</strong> معنى الحياة والطاقة والاحتفال.</p>
                    <p>معاً، تعني أتوليفا <strong>إحياء تجربة المالديف</strong>، لا باعتبارها شواطئ ومنتجعات فاخرة فقط، بل كبلد يضم جزرًا محلية وبيوت ضيافة ورحلات قوارب وحياة بحرية مذهلة وتجارب مختلفة من جزيرة إلى أخرى.</p>
                    <p>نراعي احتياجات المسافرين المسلمين والعائلات بقدر كبير من الاهتمام. أخبرنا بتفضيلاتك، سواء كانت طعاماً حلالاً أو خصوصية أو أجواء مناسبة للعائلة أو ترتيبات خاصة، وسنبذل قصارى جهدنا للعثور على الخيارات المناسبة لك.</p>
                    <p>سواء كنت تبحث عن منتجع فاخر، أو عطلة اقتصادية في جزيرة محلية، أو رحلة قارب للمغامرة، أو باقة منظمة بعناية، يمكننا مساعدتك. نؤمن بأن المالديف يجب أن تكون متاحة أيضاً للمسافر الذي يبحث عن قيمة جيدة وميزانية مدروسة.</p>
                    <p>اتصل بنا أو راسلنا عبر واتساب، وشاركنا تواريخك وميزانيتك وما تتخيله لعطلتك. سنساعدك على جعل اكتشاف المالديف وتخطيطها أسهل وأكثر وضوحاً.</p>
                    <p class="about-story__signature"><strong>أتوليفا المالديف — المالديف كما تحلم بها، مخططة بعناية.</strong></p>
                @else
                    <p>Atolliva Maldives was created from a simple idea: to help travellers experience the Maldives in a more personal, thoughtful, and reliable way.</p>
                    <p>The name <strong>Atolliva</strong> was inspired by the very thing that makes the Maldives unique — its <strong>atolls</strong>.</p>
                    <p>We wanted a name that immediately felt connected to the Maldives, but was also distinctive enough to become its own identity. <strong>“Atoll”</strong> represents our islands, lagoons, reefs and the communities spread across the country. <strong>“Viva”</strong> brings a sense of life, energy and celebration.</p>
                    <p>Together, <strong>Atolliva</strong> represents the idea of <strong>bringing the Maldives to life</strong>.</p>
                    <p>For us, the Maldives is much more than luxury resorts and beautiful beaches. It is hundreds of islands, local communities, guesthouses, liveaboards, incredible marine life and experiences that can be completely different from one atoll to another.</p>
                    <p>That is what Atolliva Maldives aims to connect travellers with.</p>
                    <p>Whether you are looking for a luxury resort escape, an affordable local-island holiday, a liveaboard adventure or a carefully planned Maldives package, our goal is to make discovering and planning your trip simple.</p>
                    <p class="about-story__signature"><strong>Atolliva Maldives — Your Maldives, thoughtfully planned</strong></p>
                @endif
            </div>
        </div>
    </section>

    <section class="about-highlights">
        <div class="about-highlights__inner">
            <article class="about-card">
                <p class="kicker">{{ $isArabic ? 'لماذا نحن هنا' : 'WHY WE EXIST' }}</p>
                <h2>{{ $isArabic ? 'تخطيط سفر واضح بمعرفة محلية.' : 'Travel planning with local clarity.' }}</h2>
                <p>{{ $isArabic ? 'نشاركك معرفة واقعية عن الإقامات والطرق والجزر المرجانية والتنقلات، حتى تتجاوز قرارات الحجز العامة وتختار التجربة المناسبة لرحلتك.' : 'We help travellers move beyond generic booking decisions by sharing grounded Maldives knowledge on stays, routes, atolls, transfers, and the kind of experience that suits each trip best.' }}</p>
            </article>

            <article class="about-card">
                <p class="kicker">{{ $isArabic ? 'ما الذي نقدمه' : 'WHAT WE CONNECT' }}</p>
                <h2>{{ $isArabic ? 'من الفخامة إلى العطلات الاقتصادية.' : 'Resorts, guesthouses, liveaboards, and more.' }}</h2>
                <p>{{ $isArabic ? 'من الإقامات الفاخرة في الجزر إلى بيوت الضيافة المحلية بأسعار مناسبة ومغامرات البحر، نساعدك في فهم خيارات المالديف وحجزها بثقة، وفق ميزانيتك.' : 'From luxury island stays to local-island holidays and ocean adventures, we aim to make the full Maldives feel easier to understand and easier to book with confidence.' }}</p>
            </article>
        </div>
    </section>

    <section class="about-contact">
        <div class="about-contact__inner">
            <div class="about-contact__copy">
                <p class="kicker">{{ $isArabic ? 'مكتبنا' : 'OUR OFFICE' }}</p>
                <h2>{!! $isArabic ? 'زرنا أو تواصل مع<br><em>أتوليفا المالديف.</em>' : 'Visit or contact<br><em>Atolliva Maldives.</em>' !!}</h2>
                <p>{{ $isArabic ? 'إذا رغبت في المساعدة لتخطيط رحلتك، أو مناقشة خيارات السفر، أو معرفة التجربة الأنسب لك في المالديف، يسعدنا دائماً أن نسمع منك.' : 'If you would like help planning your trip, discussing travel products, or understanding which Maldives experience is right for you, we are always happy to hear from you.' }}</p>
            </div>

            <div class="about-contact__card">
                <p class="about-contact__label">{{ $isArabic ? 'عنوان المكتب' : 'Office address' }}</p>
                <p class="about-contact__address">{{ $officeAddress }}</p>
                <a class="about-contact__cta" href="https://wa.me/{{ env('MICRO_TRAVEL_WHATSAPP','9609996210') }}?text={{ urlencode($isArabic ? 'مرحباً أتوليفا المالديف، أود المساعدة في تخطيط عطلتي في المالديف.' : 'Hello Atolliva Maldives, I would like help planning my Maldives holiday.') }}" target="_blank">{{ $isArabic ? 'تواصل معنا عبر واتساب' : 'Send an inquiry' }} <span>{{ $isArabic ? '↗' : '→' }}</span></a>
            </div>
        </div>
    </section>
</main>

@include('partials.site-footer')
@endsection
