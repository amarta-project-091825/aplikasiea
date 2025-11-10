<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Edit Anggota
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form action="{{ route('admin.users.update', $user->_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $user->name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $user->email) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('email')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role_id" id="role_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->_id }}" 
                                {{ old('role_id', $user->role_id) == $role->_id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-xs text-gray-400">(kosongkan kalau tidak ingin diubah)</span></label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('password')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.users.index') }}"
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
