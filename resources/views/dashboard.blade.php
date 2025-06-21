<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    Witaj z powrotem, {{ auth()->user()->name }}!
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liczba Twoich Projektów</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $projectCount }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Łączna Liczba Zadań</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $taskCount }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Ostatnio Dodane Zadania</h3>
                    <div class="space-y-4">
                        @forelse ($recentTasks as $task)
                            <div class="p-4 border dark:border-gray-700 rounded-lg flex justify-between items-center">
                                <div>
                                    <a href="{{ route('projects.show', $task->project) }}" class="font-bold hover:underline">{{ $task->title }}</a>
                                    <p class="text-sm text-gray-500">w projekcie: {{ $task->project->name }}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full">{{ $task->status->label() }}</span>
                                </div>
                            </div>
                        @empty
                            <p>Brak zadań do wyświetlenia.</p>
                        @endforelse
                    </div>
                    <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4">Zadania wg Statusu</h3>
                            <div style="height: 300px;">
                                <canvas id="tasksChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('tasksChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // lub 'bar' dla wykresu słupkowego
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Liczba zadań',
                        data: @json($chartData),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        });
    </script>
</x-app-layout>
