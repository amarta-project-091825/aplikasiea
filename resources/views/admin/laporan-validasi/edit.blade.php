<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Validasi Laporan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Laporan</h3>
                <h3 class="text-lg font-medium mb-4">Riwayat Status</h3>
<ul class="divide-y divide-gray-200 dark:divide-gray-700">
    @foreach($laporan->history as $h)
        <li class="py-2 text-sm text-gray-700 dark:text-gray-300">
            <span class="font-semibold">{{ $h->status_label }}</span>
            <span class="ml-2 text-gray-500">{{ $h->changed_at->format('d M Y H:i') }}</span>
        </li>
    @endforeach
</ul>

                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($laporan->decoded as $key => $val)
                        <div class="py-3">
                            <dt class="font-semibold text-gray-700 dark:text-gray-300">
                                {{ ucfirst(str_replace('_',' ', $key)) }}
                            </dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                @if(is_array($val) && isset($val['mime']))
                                    @if(Str::startsWith($val['mime'], 'image/'))
                                        <img src="{{ $val['data'] }}" alt="{{ $val['name'] }}" class="h-40 rounded border mt-2">
                                    @else
                                        <a href="{{ $val['data'] }}" 
                                           download="{{ $val['name'] }}" 
                                           class="inline-block mt-1 text-indigo-600 dark:text-indigo-400 underline">
                                            {{ $val['name'] }}
                                        </a>
                                    @endif
                                @else
                                    {{ $val }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <form method="POST" action="{{ route('admin.laporan-validasi.update', $laporan->_id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pilih Status
                        </label>
                        <select name="status_id" 
                                class="w-full border rounded p-2 dark:bg-gray-700 dark:text-gray-100">
                            @foreach($statusList as $s)
                                <option value="{{ $s->_id }}" {{ $laporan->status_id == $s->_id ? 'selected' : '' }}>
                                    {{ $s->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
