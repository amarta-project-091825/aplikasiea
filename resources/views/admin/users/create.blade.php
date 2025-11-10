<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Tambah Anggota') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="p-6">
            <form action="{{ route('admin.users.store') }}" method="post">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 p-2 border border-red-400 bg-red-100 text-red-700">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />

                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />

                <x-input-label for="role_id" :value="__('Role')" />
              <select name="role_id" id="role_id" class="mt-1 block w-full" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->_id }}" @selected(old('role_id', $user->role_id ?? null) == $role->_id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-label for="password" :value="__('Password (opsional)')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />

                <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
            </form>

                </div>
            </div>

            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                *Ini masih UI saja. Nanti tombol “Simpan” dihubungkan ke controller.
            </div>
        </div>
        @if ($errors->any())
            <div class="mb-4 p-2 border border-red-400 bg-red-100 text-red-700">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</x-app-layout>
