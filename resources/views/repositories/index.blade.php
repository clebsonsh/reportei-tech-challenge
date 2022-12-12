<x-app-layout>
    <div class="flex justify-end">
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault();
                                this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        @endauth
    </div>
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
