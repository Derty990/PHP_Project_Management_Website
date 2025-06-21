<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Zadanie: {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-100">
                <p>{{ $task->description }}</p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-bold mb-4">Załączniki</h3>

                <form action="{{ route('tasks.attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <div class="flex items-center space-x-2">
                        <input type="file" name="attachment" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                        <x-primary-button>Dodaj plik</x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                </form>

                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    @forelse($task->attachments as $attachment)
                        <li class="relative group border rounded-lg overflow-hidden">

                            @if ($attachment->isImage())
                                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" data-lightbox="attachments">
                                    <img src="{{ asset('storage/' . $attachment->path) }}" alt="{{ $attachment->original_name }}" class="h-60 w-full object-cover transition-transform group-hover:scale-105">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="flex flex-col items-center justify-center h-60 w-full bg-gray-100 dark:bg-gray-700 p-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600">
                                    <svg class="w-16 h-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    <span class="text-xs text-gray-500 dark:text-gray-300 mt-2 break-all">{{ $attachment->original_name }}</span>
                                </a>
                            @endif

                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten załącznik?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 bg-red-600/75 hover:bg-red-700 text-white rounded-full leading-none">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="col-span-full text-gray-500">Brak załączników.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <!-- Karta z Komentarzami -->
        <div class="p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-bold mb-4">Komentarze</h3>

            <!-- Formularz dodawania nowego komentarza -->
            @can('addComment', $task)
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mb-6">
                @csrf
                <textarea name="body" rows="3" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" placeholder="Dodaj komentarz..."></textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                <x-primary-button class="mt-2">Dodaj komentarz</x-primary-button>
            </form>
            @endcan

            <!-- Lista istniejących komentarzy -->
            <div class="space-y-4">
                @forelse ($task->comments as $comment)
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <!-- Możesz tu w przyszłości dodać awatary użytkowników -->
                            <div class="h-10 w-10 bg-gray-300 dark:bg-gray-700 rounded-full flex items-center justify-center font-bold text-gray-600 dark:text-gray-300">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $comment->body }}</p>

                            @can('delete', $comment)
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="text-right">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Usuń</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Brak komentarzy.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
