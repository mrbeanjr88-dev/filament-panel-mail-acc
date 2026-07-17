<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">

        {{ $this->form }}

        <div class="sticky bottom-0 z-10 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-4
                    bg-white/80 dark:bg-gray-900/80 backdrop-blur border-t
                    border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between max-w-full">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Changes are applied immediately after saving.
                </p>
                <x-filament::button
                    type="submit"
                    icon="heroicon-o-check"
                    size="md"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Save settings</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </x-filament::button>
            </div>
        </div>

    </form>
</x-filament-panels::page>
