<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Anggota') }}
        </h2>
    </x-slot>

    @php
        $roleBadge = fn($id) => match((int)$id) {
            1 => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200',
            2 => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
            3 => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
        };
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Anggota</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pengguna dan peran (role).</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    + Tambah Anggota
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $u)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $u->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs px-2 py-1 rounded {{ $roleBadge($u->role_id) }}">
                                            {{ $u->role?->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="inline-flex gap-2">
                                            @php
                                                $protectedId = '69095e2bca6db208210cd1c2';
                                            @endphp
                                            @if((string) $u->_id !== $protectedId)
                                                <a href="{{ route('admin.users.edit', $u->_id) }}"
                                                class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.users.destroy', $u->_id) }}" method="POST" 
                                                    onsubmit="return confirm('Hapus anggota ini?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-1 rounded border border-red-300 text-red-700 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($users->isEmpty())
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada anggota.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
