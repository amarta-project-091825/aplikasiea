<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Form
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form action="{{ route('admin.form.update', $form->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama_jalan" class="block text-sm font-medium text-gray-700">Nama Jalan</label>
                    <input type="text" name="nama_jalan" id="nama_jalan"
                           value="{{ old('nama_jalan', $form->nama_jalan) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('nama_jalan')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.form.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded-md text-sm">Batal</a>
                    <button type="submit"
                            class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
