<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoManager;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __invoke(SeoManager $seoManager): View
    {
        $sections = [
            [
                'title' => 'Maldives Travel Questions',
                'intro' => 'Helpful answers about getting to the Maldives, moving between islands, choosing the right season, and knowing what to expect before you travel.',
                'items' => [
                    [
                        'question' => 'Where are the Maldives located?',
                        'answer' => [
                            'The Maldives is a tropical island nation in the Indian Ocean, located southwest of India and Sri Lanka.',
                            'The country is made up of around 1,200 coral islands spread across a chain of beautiful atolls.',
                            'With turquoise lagoons, white-sand beaches, and warm tropical weather, the Maldives is one of the world\'s most popular island holiday destinations.',
                        ],
                    ],
                    [
                        'question' => 'How do I travel around the Maldives?',
                        'answer' => [
                            'International visitors normally arrive by air. From the international airport, onward transfers to resorts and local islands may be arranged by speedboat, seaplane, domestic flight, local ferry, or traditional Maldivian dhoni.',
                            'The type of transfer depends mainly on the location of your resort or island.',
                            'Atolliva Maldives can help coordinate the most suitable transfer together with your accommodation booking.',
                        ],
                    ],
                    [
                        'question' => 'What is the best time to visit the Maldives?',
                        'answer' => [
                            'The Maldives can be visited throughout the year.',
                            'November to April is generally considered the drier and more popular travel period, with increased demand for resorts and accommodation.',
                            'May to October normally sees more rainfall and occasional wind, but it can also be an excellent time to find attractive resort offers and enjoy a quieter Maldives experience.',
                            'Because temperatures remain warm throughout the year, there really is not a bad time to experience the Maldives.',
                        ],
                    ],
                    [
                        'question' => 'What time zone does the Maldives use?',
                        'answer' => [
                            'The Maldives operates on GMT/UTC +5.',
                            'Some resorts may use their own island time, usually adjusting the clock slightly to provide guests with longer daylight hours.',
                        ],
                    ],
                    [
                        'question' => 'What is the main airport in the Maldives?',
                        'answer' => [
                            'The main international gateway to the Maldives is Velana International Airport, located on Hulhule Island close to Male.',
                            'After arrival, travellers continue to their resorts or islands by speedboat, seaplane, or domestic flight depending on the destination.',
                        ],
                    ],
                    [
                        'question' => 'What happens when I arrive at the airport?',
                        'answer' => [
                            'After completing immigration and collecting your luggage, you will normally meet your resort representative or transfer coordinator.',
                            'If you book your trip through Atolliva Maldives, we can help organise your accommodation and transfer arrangements so that your journey from the airport to your destination is as smooth as possible.',
                        ],
                    ],
                    [
                        'question' => 'How does domestic transportation work?',
                        'answer' => [
                            'Transportation between Maldivian islands varies depending on distance and location.',
                            'Common options include domestic flights, speedboats, seaplanes, public ferries, and private boat transfers.',
                            'Remote resorts are commonly reached by domestic flight or seaplane, while resorts closer to Male may be accessible by speedboat.',
                        ],
                    ],
                    [
                        'question' => 'What is a Maldives resort island?',
                        'answer' => [
                            'One of the things that makes the Maldives unique is its famous one island, one resort concept.',
                            'Many resorts occupy their own private island, giving guests access to beaches, lagoons, restaurants, activities, and accommodation within a single island environment.',
                            'Accommodation can range from comfortable beach villas to luxury overwater villas with private pools.',
                        ],
                    ],
                    [
                        'question' => 'What is the weather like in the Maldives?',
                        'answer' => [
                            'The Maldives has a warm tropical climate throughout the year, with average daytime temperatures generally around 30°C and sea temperatures suitable for swimming through most of the year.',
                            'November to April generally brings drier weather, while May to October brings a greater chance of rain and wind.',
                            'Even during the wetter months, rainfall often comes in short tropical showers rather than continuous rain.',
                        ],
                    ],
                    [
                        'question' => 'Do I need a visa to visit the Maldives?',
                        'answer' => [
                            'Tourists generally do not need to obtain a tourist visa before travelling to the Maldives. Entry permission is normally granted on arrival, subject to Maldives immigration requirements.',
                            'Travellers should make sure they meet the latest passport, accommodation, return-ticket, and entry requirements before departure.',
                        ],
                    ],
                    [
                        'question' => 'What is the capital of the Maldives?',
                        'answer' => [
                            'The capital city is Male.',
                            'Male is the administrative and commercial centre of the Maldives and offers shops, restaurants, markets, mosques, offices, and other local attractions.',
                            'It provides a very different experience from a private resort island and is a good place to experience everyday Maldivian city life.',
                        ],
                    ],
                    [
                        'question' => 'Is there shopping in the Maldives?',
                        'answer' => [
                            'Yes. Male and other populated islands offer local shops, supermarkets, souvenir stores, and boutiques.',
                            'Popular souvenirs include local handicrafts, Maldivian artwork, clothing, gifts, packaged local products, and handmade items.',
                            'Many resorts also operate their own boutiques and souvenir shops.',
                        ],
                    ],
                    [
                        'question' => 'What currency is used in the Maldives?',
                        'answer' => [
                            'The local currency is the Maldivian Rufiyaa (MVR).',
                            'US Dollars are also widely accepted within the tourism industry, particularly at resorts.',
                            'Exchange rates may change, so travellers should check current rates when planning their trip.',
                        ],
                    ],
                    [
                        'question' => 'Can I use credit cards in the Maldives?',
                        'answer' => [
                            'Yes. Major credit and debit cards are widely accepted at most resorts, hotels, restaurants, and tourism-related businesses.',
                            'Cash may still be useful when visiting local islands, small cafes, markets, or smaller establishments.',
                        ],
                    ],
                    [
                        'question' => 'Is the Maldives safe for tourists?',
                        'answer' => [
                            'The Maldives is generally considered a safe destination for international travellers.',
                            'As with any overseas destination, visitors should take normal precautions, keep valuables secure, and follow local laws, resort safety instructions, and travel advice.',
                        ],
                    ],
                    [
                        'question' => 'What medical facilities are available?',
                        'answer' => [
                            'Male and other major population centres have hospitals, clinics, and pharmacies.',
                            'Many resorts also have medical personnel or access to nearby healthcare facilities.',
                            'Travel insurance that includes medical treatment and emergency evacuation is strongly recommended, particularly for diving, water sports, and remote island stays.',
                        ],
                    ],
                    [
                        'question' => 'What should I wear in the Maldives?',
                        'answer' => [
                            'Light, breathable clothing is ideal because of the tropical climate.',
                            'At private resort islands, normal beach and resort wear is generally acceptable.',
                            'When visiting local inhabited islands or Male, visitors should respect Maldivian culture by dressing modestly.',
                            'Swimwear should normally be worn only in designated beach areas, resort islands, or approved tourist beaches.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'About Atolliva Maldives',
                'intro' => 'Answers about what Atolliva Maldives does, how we help travellers plan, and what kind of holidays, transfers, and services we can arrange.',
                'items' => [
                    [
                        'question' => 'What is Atolliva Maldives?',
                        'answer' => [
                            'Atolliva Maldives is a Maldives-focused travel company helping travellers discover and book unforgettable island holidays.',
                            'We help guests find suitable resorts, guesthouses, liveaboards, transfers, and Maldives experiences based on their preferences and budget.',
                            'Our goal is simple: make planning a Maldives holiday easier, clearer, and more personal.',
                        ],
                    ],
                    [
                        'question' => 'Is Atolliva Maldives a Maldives travel company?',
                        'answer' => [
                            'Yes. Atolliva Maldives is focused specifically on travel within the Maldives, giving guests access to local destination knowledge and personalised assistance when planning their holiday.',
                        ],
                    ],
                    [
                        'question' => 'What services does Atolliva Maldives offer?',
                        'answer' => [
                            'Atolliva Maldives can assist with Maldives resort bookings, guesthouse holidays, honeymoon packages, family holidays, luxury holidays, budget-friendly Maldives stays, liveaboard holidays, speedboat transfers, seaplane transfers, domestic flight arrangements, excursions, experiences, and customised Maldives itineraries.',
                        ],
                    ],
                    [
                        'question' => 'Can Atolliva Maldives help me choose a resort?',
                        'answer' => [
                            'Yes. With so many resorts in the Maldives, choosing the right one can sometimes be difficult.',
                            'Tell us your travel dates, number of travellers, budget, preferred villa type, meal plan, activities, and travel style, and we can help shortlist resorts that suit your requirements.',
                        ],
                    ],
                    [
                        'question' => 'Does Atolliva Maldives arrange honeymoon holidays?',
                        'answer' => [
                            'Yes. We can help couples plan memorable Maldives honeymoons including romantic resorts, overwater villas, private pool villas, candlelight dinners, honeymoon benefits, couple experiences, spa treatments, excursions, and resort transfers.',
                        ],
                    ],
                    [
                        'question' => 'Can families book through Atolliva Maldives?',
                        'answer' => [
                            'Absolutely. We can recommend family-friendly Maldives resorts and guesthouses based on the age of your children and the type of holiday you are looking for.',
                            'We can also help identify properties offering facilities such as kids clubs, family villas, children\'s pools, and family-friendly activities.',
                        ],
                    ],
                    [
                        'question' => 'Can Atolliva Maldives arrange budget Maldives holidays?',
                        'answer' => [
                            'Yes. A Maldives holiday does not always have to mean an expensive luxury resort.',
                            'We can help travellers explore local island guesthouses, affordable resorts, special resort offers, value-for-money meal plans, and budget transfer options.',
                        ],
                    ],
                    [
                        'question' => 'Does Atolliva Maldives offer luxury holidays?',
                        'answer' => [
                            'Yes. For travellers looking for something exceptional, we can help arrange luxury Maldives experiences including premium overwater villas, private pools, luxury resorts, private transfers, fine dining, spa experiences, private excursions, and exclusive island experiences.',
                        ],
                    ],
                    [
                        'question' => 'Can I book a liveaboard through Atolliva Maldives?',
                        'answer' => [
                            'Yes. Liveaboards are an excellent way to explore different parts of the Maldives, particularly for diving, snorkelling, surfing, and ocean adventures.',
                            'Atolliva Maldives can help you explore available liveaboard options based on your travel dates and interests.',
                        ],
                    ],
                    [
                        'question' => 'Can you arrange airport transfers?',
                        'answer' => [
                            'Yes. Depending on the location of your accommodation, transfers may include speedboat, seaplane, domestic flight, domestic flight plus speedboat, or private boat transfer.',
                            'We can help coordinate the required transfer as part of your booking.',
                        ],
                    ],
                    [
                        'question' => 'Is Atolliva Maldives suitable for first-time visitors?',
                        'answer' => [
                            'Yes, and this is where local guidance can be especially useful.',
                            'If this is your first Maldives holiday, we can help you understand which resort or island to choose, beach villa versus water villa, meal plan options, transfer types, seaplane requirements, best travel periods, activities and excursions, resort costs, and local island versus resort island holidays.',
                        ],
                    ],
                    [
                        'question' => 'Can Atolliva Maldives customise my holiday?',
                        'answer' => [
                            'Yes. Rather than offering the same package to everyone, we can tailor recommendations according to your budget, travel dates, interests, preferred resort style, villa preference, meal plan, activities, and special occasions.',
                        ],
                    ],
                    [
                        'question' => 'Can you arrange Maldives trips for groups?',
                        'answer' => [
                            'Yes. We can assist with holidays for families, groups of friends, corporate groups, wedding groups, celebration trips, diving groups, and special-interest travellers.',
                        ],
                    ],
                    [
                        'question' => 'Can Atolliva Maldives arrange private island experiences?',
                        'answer' => [
                            'Depending on availability and requirements, we can help source premium resorts, private island stays, and exclusive accommodation options for travellers seeking additional privacy and luxury.',
                        ],
                    ],
                    [
                        'question' => 'Do you provide support during my Maldives holiday?',
                        'answer' => [
                            'Yes. Our aim is not simply to make a booking and disappear.',
                            'Atolliva Maldives can assist with your travel arrangements before arrival and help with booking-related matters during your holiday.',
                        ],
                    ],
                    [
                        'question' => 'Why book through Atolliva Maldives instead of booking everything myself?',
                        'answer' => [
                            'Online booking websites are useful, but the Maldives is different from many other destinations.',
                            'The resort you select affects your transfer method, meal costs, activities, villa experience, and overall holiday budget.',
                            'Atolliva Maldives provides human, Maldives-focused assistance to help you make a more informed decision.',
                        ],
                    ],
                    [
                        'question' => 'Does Atolliva Maldives work with different resorts?',
                        'answer' => [
                            'Our goal is to provide travellers with a range of suitable Maldives accommodation options rather than promoting only one type of property.',
                            'Availability, pricing, and offers will depend on your travel dates.',
                        ],
                    ],
                    [
                        'question' => 'Can you help me find Maldives special offers?',
                        'answer' => [
                            'Yes. Resorts regularly offer promotions such as early-booking discounts, free nights, honeymoon benefits, complimentary transfers, meal-plan upgrades, children\'s offers, and seasonal promotions.',
                            'Contact Atolliva Maldives with your travel dates and we can help identify suitable available offers.',
                        ],
                    ],
                    [
                        'question' => 'How far in advance should I book?',
                        'answer' => [
                            'For peak travel periods, particularly December, Christmas, New Year, Easter, and other popular holiday dates, booking well in advance is recommended.',
                            'For other dates, last-minute offers may occasionally be available.',
                        ],
                    ],
                    [
                        'question' => 'How do I request a Maldives holiday quotation?',
                        'answer' => [
                            'Simply send Atolliva Maldives your travel dates, number of adults, number and ages of children, approximate budget, preferred accommodation or resort style, and any special requirements.',
                            'We can then recommend suitable options.',
                        ],
                    ],
                    [
                        'question' => 'How can I contact Atolliva Maldives?',
                        'answer' => [
                            'You can contact Atolliva Maldives through our website enquiry form, WhatsApp, or email.',
                            'Tell us what kind of Maldives holiday you have in mind, and we will help you explore suitable options.',
                        ],
                    ],
                    [
                        'question' => 'Why choose Atolliva Maldives?',
                        'answer' => [
                            'Because planning a Maldives holiday should not be complicated.',
                            'Local knowledge. Better choices. Personal assistance. One unforgettable Maldives experience.',
                            'Atolliva Maldives. Your Maldives journey starts here.',
                        ],
                    ],
                ],
            ],
        ];

        return view('faq', [
            'sections' => $sections,
            'faqSchema' => collect($sections)
                ->flatMap(fn (array $section) => $section['items'])
                ->map(fn (array $item) => [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => implode(' ', $item['answer']),
                    ],
                ])
                ->values(),
            'seo' => $seoManager->forSimplePage(
                title: 'Maldives FAQ | Atolliva Maldives Travel Questions',
                description: 'Find answers to common Maldives travel questions about transfers, weather, visas, resorts, liveaboards, budgets, honeymoons, and planning with Atolliva Maldives.',
                canonical: route('faq'),
                breadcrumbs: [
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'FAQ', 'url' => route('faq')],
                ],
                extraSchema: [[
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($sections)
                        ->flatMap(fn (array $section) => $section['items'])
                        ->map(fn (array $item) => [
                            '@type' => 'Question',
                            'name' => $item['question'],
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => implode(' ', $item['answer']),
                            ],
                        ])
                        ->all(),
                ]],
            )->toArray(),
        ]);
    }
}
