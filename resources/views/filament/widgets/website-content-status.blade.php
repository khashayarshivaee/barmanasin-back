<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Website Content
        </x-slot>

        <x-slot name="description">
            {{ $activeCount }} of {{ count($items) }} homepage sections active
        </x-slot>

        <div
            style="
                display: flex;
                flex-direction: column;
                gap: 0;
            "
        >
            @foreach ($items as $item)
                <a
                    href="{{ $item['url'] }}"
                    style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 1rem;
                        min-height: 3.5rem;
                        padding: 0.8rem 0;
                        color: inherit;
                        text-decoration: none;
                        border-bottom: {{ $loop->last ? '0' : '1px solid rgba(148, 163, 184, 0.16)' }};
                    "
                >
                    <div
                        style="
                            min-width: 0;
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                        "
                    >
                        <div
                            style="
                                width: 0.45rem;
                                height: 0.45rem;
                                flex: 0 0 auto;
                                border-radius: 9999px;
                                background: {{ $item['active'] ? '#22c55e' : '#f59e0b' }};
                                box-shadow: 0 0 0 4px {{ $item['active'] ? 'rgba(34, 197, 94, 0.10)' : 'rgba(245, 158, 11, 0.10)' }};
                            "
                        ></div>

                        <div style="min-width: 0;">
                            <div
                                style="
                                    font-size: 0.875rem;
                                    font-weight: 600;
                                    line-height: 1.25rem;
                                "
                            >
                                {{ $item['label'] }}
                            </div>

                            <div
                                style="
                                    margin-top: 0.1rem;
                                    font-size: 0.72rem;
                                    line-height: 1rem;
                                    opacity: 0.55;
                                "
                            >
                                {{ $item['description'] }}
                            </div>
                        </div>
                    </div>

                    <x-filament::badge
                        :color="$item['active'] ? 'success' : 'warning'"
                        size="sm"
                    >
                        {{ $item['active'] ? 'Active' : 'Inactive' }}
                    </x-filament::badge>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
