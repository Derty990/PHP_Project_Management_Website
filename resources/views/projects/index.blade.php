<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Moje Projekty') }}
        </h2>
    </x-slot>

    <form method="GET" action="{{ route('projects.index') }}" class="mb-4">
        <div class="flex">
            <x-text-input name="search" class="w-full" placeholder="Szukaj po nazwie projektu..." value="{{ request('search') }}" />
            <x-primary-button class="ms-2">Szukaj</x-primary-button>
        </div>
    </form>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            <span class="font-medium">Sukces!</span> {{ session('success') }}
                        </div>
                    @endif
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Dodaj projekt</a>

                    <ul class="mt-4 space-y-4">
                        <form method="GET" action="{{ route('projects.index') }}" class="mb-6">
                            <div class="flex">
                                <x-text-input name="search" class="w-full" placeholder="Szukaj po nazwie projektu..." value="{{ request('search') }}" />
                                <x-primary-button class="ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                    <span class="ms-2">Szukaj</span>
                                </x-primary-button>
                            </div>
                        </form>
                        @forelse ($projects as $project)
                            <li class="p-4 border rounded-lg flex justify-between items-center">
                                <div>
                                    <a href="{{ route('projects.show', $project) }}" class="text-lg font-bold hover:underline">{{ $project->name }}</a>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $project->description }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('projects.edit', $project) }}" class="text-blue-600 hover:text-blue-900">Edytuj</a>

                                    <form method="POST" action="{{route('projects.destroy', $project)}}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Usuń
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <p>Brak projektów do wyświetlenia.</p>
                        @endforelse
                    </ul>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
