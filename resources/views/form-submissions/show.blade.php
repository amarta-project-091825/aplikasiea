<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Submission Detail
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Form: {{ $submission->form?->name ?? '-' }}
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Submitted by: {{ $submission->submitted_by }} | Tanggal: {{ $submission->created_at }}
            </p>

            <div class="grid gap-4">
                @foreach($submission->data as $field => $value)
                    <div class="p-2 border rounded bg-gray-50 dark:bg-gray-900/40">
                        <strong class="text-gray-700 dark:text-gray-200">{{ $field }}:</strong>
                        <span class="text-gray-900 dark:text-gray-100">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                <a href="{{ route('form-submissions.index') }}" class="text-indigo-600 hover:underline">← Kembali ke list</a>
            </div>
        </div>
    </div>
</x-app-layout>
