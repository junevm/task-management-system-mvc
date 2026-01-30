<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Create New Task
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($tasks->isEmpty())
                        <p class="text-gray-500 text-center py-8">No tasks found. Create your first task!</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($tasks as $task)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $task->title }}
                                            </a>
                                            @if ($task->description)
                                                <p class="text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $task->status->color() }}-100 text-{{ $task->status->color() }}-800">
                                                    {{ $task->status->label() }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $task->priority->color() }}-100 text-{{ $task->priority->color() }}-800">
                                                    Priority: {{ $task->priority->label() }}
                                                </span>
                                                @if ($task->due_date)
                                                    <span class="text-xs text-gray-500">
                                                        Due: {{ $task->due_date->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex gap-2 ml-4">
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
