<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Wszystkie Moje Zadania') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('tasks.index') }}" class="mb-6">
                        <div class="flex">
                            <x-text-input name="search" class="w-full" placeholder="Szukaj po nazwie zadania..." value="{{ request('search') }}" />
                            <x-primary-button class="ms-2">Szukaj</x-primary-button>
                        </div>
                    </form>
                    <a href="{{ route('tasks.export.csv') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                        Eksportuj do CSV
                    </a>

                    <div class="space-y-4">
                        @forelse ($tasks as $task)
                            <div class="p-4 border dark:border-gray-700 rounded-lg flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold">{{ $task->title }}</h4>
                                    <p class="text-sm text-gray-500">
                                        W projekcie: <a href="{{ route('projects.show', $task->project) }}" class="underline hover:text-blue-500">{{ $task->project->name }}</a>
                                    </p>
                                    <div class="mt-3 flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">{{ $task->status->label() }}</span>
                                        <span class="px-2 py-1 text-xs font-semibold text-white bg-gray-500 rounded-full">{{ $task->priority->label() }}</span>
                                    </div>
                                </div>
                                @can('update', $task)
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900">Edytuj</a>
                                    </div>
                                @endcan
                            </div>
                        @empty
                            <p>Nie znaleziono zadań pasujących do kryteriów.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
