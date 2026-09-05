<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <x-slot name="description">
            Manage the most important website content.
        </x-slot>

        <div
            style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 0.75rem;
            "
        >
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 1rem;
                        min-height: 5.25rem;
                        padding: 1rem 1.1rem;
                        color: inherit;
                        text-decoration: none;
                        border: 1px solid rgba(148, 163, 184, 0.18);
                        border-radius: 0.75rem;
                        transition:
                            border-color 160ms ease,
                            background 160ms ease,
                            transform 160ms ease;
                    "
                    onmouseover="
                        this.style.borderColor='rgba(148, 163, 184, 0.42)';
                        this.style.background='rgba(148, 163, 184, 0.05)';
                        this.style.transform='translateY(-1px)';
                    "
                    onmouseout="
                        this.style.borderColor='rgba(148, 163, 184, 0.18)';
                        this.style.background='transparent';
                        this.style.transform='translateY(0)';
                    "
                >
                    <div
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.9rem;
                            min-width: 0;
                        "
                    >
                        <div
                            style="
                                width: 2.5rem;
                                height: 2.5rem;
                                flex: 0 0 auto;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border-radius: 0.65rem;
                                background: rgba(148, 163, 184, 0.09);
                            "
                        >
                            <x-filament::icon
                                :icon="$action['icon']"
                                style="
                                    width: 1.15rem;
                                    height: 1.15rem;
                                "
                            />
                        </div>

                        <div style="min-width: 0;">
                            <div
                                style="
                                    font-size: 0.875rem;
                                    font-weight: 600;
                                    line-height: 1.2rem;
                                "
                            >
                                {{ $action['label'] }}
                            </div>

                            <div
                                style="
                                    margin-top: 0.2rem;
                                    font-size: 0.72rem;
                                    line-height: 1rem;
                                    opacity: 0.55;
                                "
                            >
                                {{ $action['description'] }}
                            </div>
                        </div>
                    </div>

                    <x-filament::icon
                        icon="heroicon-m-chevron-right"
                        style="
                            width: 1rem;
                            height: 1rem;
                            flex: 0 0 auto;
                            opacity: 0.4;
                        "
                    />
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
