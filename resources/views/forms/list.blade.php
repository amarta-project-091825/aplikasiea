<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-white">Form</h2></x-slot>
    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <ul class="list-disc ps-6">
                @forelse($forms as $f)
                    <li class="mb-2">
                        <a class="text-indigo-600" href="{{ route('forms.show', $f->slug) }}">{{ $f->name }}</a>
                        @if($f->description)
                            <div class="text-sm text-gray-500">{{ $f->description }}</div>
                        @endif
                    </li>
                @empty
                    <li>Tidak ada form aktif.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
