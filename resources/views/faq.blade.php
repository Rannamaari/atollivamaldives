@extends('layouts.app')
@section('title', $isArabic ? 'الأسئلة الشائعة عن المالديف | أتوليفا المالديف' : 'Maldives FAQ | Atolliva Maldives Travel Questions')
@section('description', $isArabic ? 'إجابات عن السفر إلى المالديف والتنقلات والتأشيرات والطقس والمنتجعات والتخطيط مع أتوليفا المالديف.' : 'Find answers to common Maldives travel questions about transfers, weather, visas, resorts, liveaboards, budgets, honeymoons, and planning with Atolliva Maldives.')
@section('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqSchema,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
@section('content')
@php
    $faqWhatsAppUrl = 'https://wa.me/'.env('MICRO_TRAVEL_WHATSAPP','9609996210').'?text='.urlencode($isArabic ? 'مرحباً أتوليفا المالديف، أود الحصول على مساعدة شخصية لتخطيط رحلتي إلى المالديف.' : 'Hello Atolliva Maldives, I have been reading your FAQ page and would like personalised help planning my Maldives trip.');
@endphp
@include('partials.site-nav', ['whatsAppText' => $isArabic ? 'مرحباً أتوليفا المالديف، أود المساعدة في تخطيط عطلتي في المالديف.' : 'Hello Atolliva Maldives, I would like help planning my Maldives holiday.'])

<main class="faq-page">
    <section class="faq-hero">
        <div class="faq-hero__backdrop"></div>
        <div class="faq-hero__inner">
            <p class="faq-hero__kicker">{{ $isArabic ? 'أسئلة شائعة عن المالديف' : 'MALDIVES FAQ' }}</p>
            <h1>{!! $isArabic ? 'أسئلة <br><em>شائعة.</em>' : 'Frequently asked<br><em>questions.</em>' !!}</h1>
            <p class="faq-hero__intro">{{ $isArabic ? 'كل ما يسأل عنه المسافرون غالباً عن المالديف، من المطار والتنقلات إلى المنتجعات والطقس ورحلات القوارب وتخطيط عطلتك مع أتوليفا المالديف.' : 'Everything travellers ask most often about the Maldives, from airports and transfers to resorts, weather, liveaboards, and planning your holiday with Atolliva Maldives.' }}</p>
        </div>
    </section>

    <section class="faq-summary">
        <div class="faq-summary__inner">
            <div>
                <p class="kicker">{{ $isArabic ? 'دليل سريع' : 'QUICK GUIDE' }}</p>
                <h2>{!! $isArabic ? 'إجابات مفيدة،<br><em>في مكان واحد.</em>' : 'Helpful answers,<br><em>all in one place.</em>' !!}</h2>
            </div>
            <p>{{ $isArabic ? 'استخدم هذه الصفحة لمعرفة الأساسيات قبل الحجز. صممت لمساعدة الزوار للمرة الأولى والمسافرين العائدين على فهم عطلات المالديف وما يمكن لأتوليفا المالديف ترتيبه لك.' : 'Use this page to explore the essentials before you book. It is designed to help both first-time visitors and returning travellers understand how Maldives holidays work and what Atolliva Maldives can arrange for you.' }}</p>
        </div>
    </section>

    <section class="faq-content">
        <div class="faq-content__inner">
            <aside class="faq-index" aria-label="{{ $isArabic ? 'أقسام الأسئلة الشائعة' : 'FAQ sections' }}">
                <p class="faq-index__title">{{ $isArabic ? 'في هذه الصفحة' : 'On this page' }}</p>
                @foreach($sections as $section)
                    <a href="#{{ \Illuminate\Support\Str::slug($section['title']) }}">{{ $section['title'] }}</a>
                @endforeach
            </aside>

            <div class="faq-groups">
                @foreach($sections as $section)
                    <section class="faq-group" id="{{ \Illuminate\Support\Str::slug($section['title']) }}">
                        <div class="faq-group__head">
                            <p class="kicker">{{ $isArabic ? ($loop->iteration === 1 ? 'خطط لرحلتك' : 'سافر مع أتوليفا') : strtoupper($loop->iteration === 1 ? 'Plan your trip' : 'Travel with Atolliva') }}</p>
                            <h2>{{ $section['title'] }}</h2>
                            <p>{{ $section['intro'] }}</p>
                        </div>

                        <div class="faq-items">
                            @foreach($section['items'] as $item)
                                <details class="faq-item" @if($loop->first && $loop->parent->first) open @endif>
                                    <summary>{{ $item['question'] }}</summary>
                                    <div class="faq-item__answer">
                                        @foreach($item['answer'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>

    <section class="faq-cta">
        <div class="faq-cta__inner">
            <p class="faq-cta__kicker">{{ $isArabic ? 'هل تحتاج إلى نصيحة شخصية؟' : 'NEED PERSONALISED ADVICE?' }}</p>
            <h2>{!! $isArabic ? 'أخبرنا بنوع<br><em>عطلة المالديف</em> التي تريدها.' : 'Tell us what kind of<br><em>Maldives holiday</em> you want.' !!}</h2>
            <p>{{ $isArabic ? 'سواء كنت تقارن بين المنتجعات أو تخطط لشهر العسل أو ترتب التنقلات أو تختار بين بيت ضيافة ورحلة قارب، تساعدك أتوليفا المالديف على تضييق الخيارات.' : 'Whether you are comparing resorts, planning a honeymoon, arranging transfers, or choosing between a guesthouse and liveaboard, Atolliva Maldives can help you narrow it down.' }}</p>
            <a href="{{ $faqWhatsAppUrl }}" target="_blank">{{ $isArabic ? 'اسألنا عبر واتساب' : 'ASK ON WHATSAPP' }} <span>↗</span></a>
        </div>
    </section>
</main>

<div class="quote-whatsapp-prompt" data-faq-whatsapp-prompt hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="faq-whatsapp-title">
    <div class="quote-whatsapp-prompt__backdrop" data-faq-whatsapp-close></div>
    <div class="quote-whatsapp-prompt__panel">
        <button class="quote-whatsapp-prompt__close" type="button" data-faq-whatsapp-close aria-label="{{ $isArabic ? 'إغلاق نافذة واتساب' : 'Close WhatsApp prompt' }}">×</button>
        <p class="quote-whatsapp-prompt__kicker">{{ $isArabic ? 'هل تحتاج إلى مساعدة شخصية؟' : 'NEED PERSONALISED HELP?' }}</p>
        <h2 id="faq-whatsapp-title">{{ $isArabic ? 'لماذا لا تتواصل معنا مباشرة عبر واتساب؟' : 'Why not ask us directly on WhatsApp?' }}</h2>
        <p>{{ $isArabic ? 'إذا كنت تتصفح الأسئلة الشائعة وتريد نصيحة تناسب خطط سفرك، يمكننا مساعدتك بصورة أسرع في محادثة مباشرة.' : 'If you have been browsing the FAQ and want advice tailored to your travel plans, we can help you much faster in a direct conversation.' }}</p>
        <div class="quote-whatsapp-prompt__actions">
            <a href="{{ $faqWhatsAppUrl }}" target="_blank" rel="noopener">{{ $isArabic ? 'راسلنا عبر واتساب ↗' : 'Message on WhatsApp ↗' }}</a>
            <button type="button" data-faq-whatsapp-close>{{ $isArabic ? 'متابعة القراءة' : 'Keep reading' }}</button>
        </div>
    </div>
</div>

@include('partials.site-footer')
<script>
(() => {
    const prompt = document.querySelector('[data-faq-whatsapp-prompt]');
    if (!prompt || window.sessionStorage.getItem('faq-whatsapp-prompt-dismissed') === '1') {
        return;
    }

    let promptTimer = null;

    const closePrompt = () => {
        prompt.hidden = true;
        prompt.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('quote-whatsapp-prompt-open');
        window.sessionStorage.setItem('faq-whatsapp-prompt-dismissed', '1');
        if (promptTimer) {
            window.clearTimeout(promptTimer);
        }
    };

    const openPrompt = () => {
        if (document.hidden) {
            return;
        }

        prompt.hidden = false;
        prompt.setAttribute('aria-hidden', 'false');
        document.body.classList.add('quote-whatsapp-prompt-open');
    };

    promptTimer = window.setTimeout(openPrompt, 30000);

    prompt.querySelectorAll('[data-faq-whatsapp-close]').forEach((element) => {
        element.addEventListener('click', closePrompt);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !prompt.hidden) {
            closePrompt();
        }
    });
})();
</script>
@endsection
