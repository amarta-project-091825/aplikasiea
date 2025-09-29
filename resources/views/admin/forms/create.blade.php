<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Form</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            {{-- Debug sementara --}}
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">Form Builder should render below.</div>

            <form method="post" action="{{ route('admin.forms.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.forms._builder', ['form' => null])
                <div class="mt-6">
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
