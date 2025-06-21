<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Projekt: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800"
                            role="alert">
                            <span class="font-medium">Sukces!</span> {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-bold">Opis projektu:</h3>
                    <p class="mb-4">{{ $project->description }}</p>

                    <hr class="my-6 border-gray-700">

                    @can('update', $project)
                        <div>
                            <h3 class="text-lg font-bold mb-2">Zarządzaj Członkami Projektu</h3>

                            <ul class="list-disc pl-5 mb-4">
                                @forelse ($project->members as $member)
                                    <li class="flex justify-between items-center py-1">
                                        <span>
                                            {{ $member->name }} ({{ $member->email }})

                                            @if($project->user_id === $member->id)
                                                <span
                                                    class="ml-2 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded-full">Właściciel</span>
                                            @else
                                                <span
                                                    class="ml-2 px-2 py-0.5 text-xs bg-gray-200 text-gray-800 rounded-full">{{ $member->pivot->role }}</span>
                                            @endif
                                        </span>

                                        @if($project->user_id !== $member->id)
                                            <form method="POST"
                                                  action="{{ route('projects.members.remove', ['project' => $project, 'user' => $member]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                                        onclick="return confirm('Czy na pewno chcesz usunąć tego członka z projektu?')">
                                                    Usuń
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                @empty
                                    <li class="list-none">Ten projekt nie ma jeszcze przypisanych członków.</li>
                                @endforelse
                            </ul>

                            <form method="POST" action="{{ route('projects.members.add', $project) }}" class="mt-6">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <x-text-input id="email" name="email" type="email" class="block w-full" placeholder="Email użytkownika" required />

                                    <select name="role_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>

                                    <x-primary-button>Dodaj członka</x-primary-button>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                            </form>
                        </div>
                    @else
                        <div>
                            <h3 class="text-lg font-bold mb-2">Członkowie Projektu</h3>
                            <ul class="list-disc pl-5 mb-4">
                                @forelse ($project->members as $member)
                                    <li>
                                        {{ $member->name }}
                                        @if($project->user_id === $member->id)
                                            <span
                                                class="ml-2 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded-full">Właściciel</span>
                                        @else
                                            <span
                                                class="ml-2 px-2 py-0.5 text-xs bg-gray-200 text-gray-800 rounded-full">{{ $member->pivot->role }}</span>
                                        @endif
                                    </li>
                                @empty
                                    <li class="list-none">Ten projekt nie ma jeszcze przypisanych członków.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endcan
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Zadania w projekcie:</h3>
                        @can('create', [App\Models\Task::class, $project])
                            <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Dodaj zadanie
                            </a>
                        @endcan
                    </div>

                    <form method="GET" action="{{ route('projects.show', $project) }}" class="mb-6">
                        <div class="flex">
                            <x-text-input name="search_tasks" class="w-full" placeholder="Szukaj po nazwie zadania w tym projekcie..." value="{{ request('search_tasks') }}" />
                            <x-primary-button class="ms-2">Szukaj</x-primary-button>
                        </div>
                    </form>

                    <div class="space-y-4">
                        @forelse ($tasks as $task)
                            <div class="p-4 border dark:border-gray-700 rounded-lg flex justify-between items-center">
                                <div>
                                    <a href="{{ route('tasks.show', $task) }}" class="font-bold text-lg hover:underline">{{ $task->title }}</a>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $task->description }}</p>

                                    <div class="mt-3 flex items-center space-x-2">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">{{ $task->status->label() }}</span>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-white bg-gray-500 rounded-full">{{ $task->priority->label() }}</span>
                                    </div>
                                </div>
                                @can('update', $task)
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('tasks.edit', $task) }}"
                                           class="text-blue-600 hover:text-blue-900">Edytuj</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Czy na pewno chcesz usunąć to zadanie?')">
                                                Usuń
                                            </button>
                                        </form>
                                    </div>
                                @endcan
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
