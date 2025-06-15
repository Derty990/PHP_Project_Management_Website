<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Projekt: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Pierwszy biały kontener: Informacje o projekcie i członkowie -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Komunikat o sukcesie (wyświetli się po dodaniu członka lub zadania) -->
                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            <span class="font-medium">Sukces!</span> {{ session('success') }}
                        </div>
                    @endif

                    <!-- Opis projektu -->
                    <h3 class="text-lg font-bold">Opis projektu:</h3>
                    <p class="mb-4">{{ $project->description }}</p>

                    <hr class="my-6 border-gray-600">

                    <!-- NOWA SEKCJA: Zarządzanie członkami projektu -->
                    @if ($project->user_id === auth()->id())
                        <div>
                            <h3 class="text-lg font-bold mb-2">Zarządzaj Członkami Projektu</h3>

                            <ul class="list-disc pl-5 mb-4">
                                @forelse ($project->members as $member)
                                    <li class="flex justify-between items-center">
                                        <span>{{ $member->name }} ({{ $member->email }}) @if($project->user_id === $member->id) <span class="text-xs text-yellow-500">(Właściciel)</span> @endif</span>

                                        @if($project->user_id !== $member->id)
                                            <form method="POST" action="{{ route('projects.members.remove', ['project' => $project, 'user' => $member]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Czy na pewno chcesz usunąć tego członka z projektu?')">
                                                    Usuń
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                @empty
                                    <li>Brak przypisanych członków.</li>
                                @endforelse
                            </ul>

                            <form method="POST" action="{{ route('projects.members.add', $project) }}">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <x-text-input id="email" name="email" type="email" class="block w-full" placeholder="Email użytkownika do dodania" required />
                                    <x-primary-button>Dodaj członka</x-primary-button>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </form>
                        </div>
                    @else
                        <div>
                            <h3 class="text-lg font-bold mb-2">Członkowie Projektu</h3>
                            <ul class="list-disc pl-5 mb-4">
                                @forelse ($project->members as $member)
                                    <li>{{ $member->name }} @if($project->user_id === $member->id) <span class="text-xs text-yellow-500">(Właściciel)</span> @endif</li>
                                @empty
                                    <li>Brak przypisanych członków.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Drugi biały kontener: Zadania w projekcie -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Nagłówek i przycisk "Dodaj zadanie" -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Zadania w projekcie:</h3>
                        <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Dodaj zadanie
                        </a>
                    </div>

                    <!-- Lista zadań -->
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
