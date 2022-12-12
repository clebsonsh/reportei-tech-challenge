<x-app-layout>
    <div class="flex justify-center">
        <div class="text-white">
            @foreach ($repositories as $repository)
                <p>
                    <a href="{{ route('repositories.show', $repository->name) }}">
                        {{ $repository->name }}
                    </a>
                </p>
            @endforeach
        </div>
    </div>
</x-app-layout>
