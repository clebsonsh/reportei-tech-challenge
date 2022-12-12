<x-app-layout>
    <div id="chart" class="w-full mb-4"></div>

    {!! $lava->renderAll() !!}
    <x-link-button class="mx-auto" href="{{ route('repositories.index') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="h-6 w-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
        </svg>

        Go back home
    </x-link-button>
</x-app-layout>
