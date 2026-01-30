<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                        <div class="flex gap-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Edit
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-gray-900">{{ $task->description ?: 'No description provided.' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $task->status->color() }}-100 text-{{ $task->status->color() }}-800">
                                    {{ $task->status->label() }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Priority</h3>
                                <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $task->priority->color() }}-100 text-{{ $task->priority->color() }}-800">
                                    {{ $task->priority->label() }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                            <p class="mt-1 text-gray-900">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date set' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created</h3>
                                <p class="mt-1 text-gray-900">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                                <p class="mt-1 text-gray-900">{{ $task->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('tasks.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Tasks</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
