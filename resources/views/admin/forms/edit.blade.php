<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Edit Form: {{ $form->name }}</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            @if(session('status'))
                <div class="mb-4 text-green-600">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('admin.forms.update', $form->_id) }}">
                @csrf
                @method('put')
                @include('admin.forms._builder', ['form' => $form])
                <div class="mt-6">
                    <x-primary-button>Update</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
