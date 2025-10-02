<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Validasi Laporan Masyarakat
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium">Nama Pelapor</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">Jenis Laporan</th>
                                <th class="px-6 py-3 text-left text-sm font-medium">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporan as $l)
                                @php
                                    $data = is_string($l->data) ? json_decode($l->data, true) : $l->data;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4">{{ $data['nama_pelapor'] ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $data['jenis_laporan'] ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">
                                            {{ optional($l->status)->label ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.laporan-validasi.edit', $l->_id) }}"
                                           class="text-indigo-600 hover:underline">Validasi</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $laporan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
