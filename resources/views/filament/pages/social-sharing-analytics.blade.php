<x-filament-panels::page>
    <div class="fi-section grid gap-6">
        @if (filled($errorMessage))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-950">Social sharing performance</h2>
                    <p class="mt-1 text-sm text-gray-500">Track which pages are being shared and which channels are generating the most sharing activity.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach(['7' => '7 Days', '30' => '30 Days', '90' => '90 Days', 'all' => 'All Time'] as $value => $label)
                        <a
                            href="{{ request()->fullUrlWithQuery(['range' => $value]) }}"
                            class="rounded-full border px-4 py-2 text-sm {{ $range === $value ? 'border-teal-600 bg-teal-50 text-teal-700' : 'border-gray-200 text-gray-600' }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
            @foreach($platformTotals as $platform => $count)
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">{{ str_replace('_', ' ', $platform) }}</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($count) }}</p>
                    <p class="mt-2 text-xs uppercase tracking-[0.18em] text-gray-400">Share actions</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-950">Visits from shared links</h3>
                <p class="text-sm text-gray-500">These numbers reflect incoming visits that arrived on UTM-tagged social share links, plus the inquiries that followed.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                <div class="rounded-xl border border-gray-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Shared-link visits</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($visitSummary['total_visits']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Shared-link inquiries</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($visitSummary['total_inquiries']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Inquiry conversion</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($visitSummary['conversion_rate'], 1) }}%</p>
                </div>

                @foreach($visitTotals as $platform => $count)
                    <div class="rounded-xl border border-gray-200 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">{{ str_replace('_', ' ', $platform) }}</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($count) }}</p>
                        <p class="mt-2 text-xs uppercase tracking-[0.18em] text-gray-400">Incoming visits</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-950">Top shared content</h3>
                <p class="text-sm text-gray-500">Updated {{ $generatedAt->format('d M Y H:i') }}</p>
            </div>

            <div class="space-y-4">
                @forelse($topContent as $row)
                    <article class="rounded-2xl border border-gray-200 bg-slate-50/70 p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-lg font-semibold text-gray-950">{{ $row['label'] }}</h4>
                                <p class="mt-1 text-sm font-medium uppercase tracking-[0.18em] text-gray-500">{{ $row['type'] }}</p>
                            </div>

                            <div class="shrink-0 rounded-xl bg-white px-4 py-3 text-right shadow-sm ring-1 ring-gray-200">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Total shares</p>
                                <p class="mt-1 text-2xl font-semibold text-gray-950">{{ number_format($row['total']) }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
                            @foreach ([
                                'WhatsApp' => $row['whatsapp'],
                                'Facebook' => $row['facebook'],
                                'X' => $row['x'],
                                'Native' => $row['native'],
                                'Copy Link' => $row['copy_link'],
                                'Copy Caption' => $row['copy_caption'],
                            ] as $label => $value)
                                <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ $label }}</p>
                                    <p class="mt-2 text-xl font-semibold text-gray-950">{{ number_format($value) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-slate-50 px-6 py-10 text-center text-gray-500">
                        No social sharing activity yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
