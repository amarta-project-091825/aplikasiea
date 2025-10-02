<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Validasi Laporan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Detail Laporan</h3>

                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($laporan->decoded as $key => $val)
                        <div class="py-2">
                            <dt class="font-semibold text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_',' ', $key)) }}</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                @if(is_array($val) && isset($val['mime']))
                                    @if(Str::startsWith($val['mime'], 'image/'))
                                        <img src="{{ $val['data'] }}" alt="{{ $val['name'] }}" class="h-40 rounded mt-2">
                                    @else
                                        <a href="{{ $val['data'] }}" download="{{ $val['name'] }}" class="text-indigo-600 underline">
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

              <form method="POST" action="{{ route('admin.laporan-validasi.update', $laporan->_id) }}">
                    @csrf
                    @method('PUT')
                    <label class="block mb-2 text-sm font-medium">Pilih Status</label>
                    <select name="status_id" class="border-gray-300 rounded-md w-full">
                        @foreach($statusList as $s)
                            <option value="{{ $s->_id }}" {{ $laporan->status_id == $s->_id ? 'selected' : '' }}>
                                {{ $s->label }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
