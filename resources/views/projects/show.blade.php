<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Projekt: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            <span class="font-medium">Sukces!</span> {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-bold">Opis projektu:</h3>
                    <p class="mb-4">{{ $project->description }}</p>

                    <hr class="my-6">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Zadania w projekcie:</h3>
                        <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Dodaj zadanie
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($project->tasks as $task)

                            <div class="p-4 border rounded-lg flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold">{{ $task->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $task->description }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-900">Edytuj</a>

                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz usunąć to zadanie?')">
                                            Usuń
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p>Brak zadań w tym projekcie.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
