<x-filament-panels::page>
    <div class="fi-section grid gap-6">
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
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-950">Top shared content</h3>
                <p class="text-sm text-gray-500">Updated {{ $generatedAt->format('d M Y H:i') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-3 pr-4 font-medium">Content</th>
                            <th class="py-3 pr-4 font-medium">Type</th>
                            <th class="py-3 pr-4 font-medium">WhatsApp</th>
                            <th class="py-3 pr-4 font-medium">Facebook</th>
                            <th class="py-3 pr-4 font-medium">X</th>
                            <th class="py-3 pr-4 font-medium">Native</th>
                            <th class="py-3 pr-4 font-medium">Copy Link</th>
                            <th class="py-3 pr-4 font-medium">Copy Caption</th>
                            <th class="py-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topContent as $row)
                            <tr>
                                <td class="py-4 pr-4 font-medium text-gray-950">{{ $row['label'] }}</td>
                                <td class="py-4 pr-4 text-gray-600">{{ $row['type'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['whatsapp'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['facebook'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['x'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['native'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['copy_link'] }}</td>
                                <td class="py-4 pr-4 text-gray-700">{{ $row['copy_caption'] }}</td>
                                <td class="py-4 font-semibold text-gray-950">{{ $row['total'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-500">No social sharing activity yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
