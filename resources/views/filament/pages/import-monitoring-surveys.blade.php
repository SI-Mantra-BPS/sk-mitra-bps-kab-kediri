<x-filament-panels::page>

    <div class="space-y-6">

        {{-- TEMPLATE --}}
        <x-filament::section>

            <x-slot name="heading">
                Template Excel
            </x-slot>

            <x-slot name="description">
                Gunakan template berikut agar format data sesuai dengan sistem.
            </x-slot>

            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg
                               bg-success-50 text-success-600
                               dark:bg-success-500/10 dark:text-success-400"
                    >
                        <x-heroicon-o-document-arrow-down class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                            Template Data Survei
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Format Excel untuk import Data Survei
                        </p>
                    </div>

                </div>

                <x-filament::button
                    wire:click="downloadTemplate"
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Download Template
                </x-filament::button>

            </div>

        </x-filament::section>


        {{-- UPLOAD --}}
        <x-filament::section>

            <x-slot name="heading">
                Upload File Excel
            </x-slot>

            <x-slot name="description">
                Upload file Excel yang telah diisi sesuai template Data Survei.
            </x-slot>

            <form wire:submit="previewData">

                {{ $this->form }}

                <div class="mt-6 flex justify-end gap-3">

                    <x-filament::button
                        type="button"
                        color="gray"
                        wire:click="cancelImport"
                    >
                        Batal
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        color="info"
                        icon="heroicon-o-eye"
                    >
                        Preview & Validasi
                    </x-filament::button>

                </div>

            </form>

        </x-filament::section>


        {{-- HASIL PREVIEW --}}
        @if ($hasPreview)

            <x-filament::section>

                <x-slot name="heading">
                    Hasil Validasi
                </x-slot>

                <x-slot name="description">
                    Periksa data berikut sebelum memasukkannya ke dalam sistem.
                </x-slot>


                {{-- STATISTIK --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    {{-- VALID --}}
                    <div
                        class="rounded-xl border border-success-200 bg-success-50 p-4
                               dark:border-success-800 dark:bg-success-950/30"
                    >
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg
                                       bg-success-100 text-success-600
                                       dark:bg-success-900/50 dark:text-success-400"
                            >
                                <x-heroicon-o-check-circle class="h-5 w-5" />
                            </div>

                            <div>
                                <p class="text-sm text-success-700 dark:text-success-300">
                                    Data Valid
                                </p>

                                <p class="mt-1 text-2xl font-bold text-success-700 dark:text-success-300">
                                    {{ count($preview) }}
                                </p>
                            </div>

                        </div>
                    </div>


                    {{-- DUPLIKAT --}}
                    <div
                        class="rounded-xl border border-warning-200 bg-warning-50 p-4
                               dark:border-warning-800 dark:bg-warning-950/30"
                    >
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg
                                       bg-warning-100 text-warning-600
                                       dark:bg-warning-900/50 dark:text-warning-400"
                            >
                                <x-heroicon-o-document-duplicate class="h-5 w-5" />
                            </div>

                            <div>
                                <p class="text-sm text-warning-700 dark:text-warning-300">
                                    Duplikat
                                </p>

                                <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">
                                    {{ count($duplicates) }}
                                </p>
                            </div>

                        </div>
                    </div>


                    {{-- INVALID --}}
                    <div
                        class="rounded-xl border border-danger-200 bg-danger-50 p-4
                               dark:border-danger-800 dark:bg-danger-950/30"
                    >
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg
                                       bg-danger-100 text-danger-600
                                       dark:bg-danger-900/50 dark:text-danger-400"
                            >
                                <x-heroicon-o-exclamation-circle class="h-5 w-5" />
                            </div>

                            <div>
                                <p class="text-sm text-danger-700 dark:text-danger-300">
                                    Tidak Valid
                                </p>

                                <p class="mt-1 text-2xl font-bold text-danger-700 dark:text-danger-300">
                                    {{ count($invalid) }}
                                </p>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- DATA VALID --}}
                @if (count($preview) > 0)

                    <div class="mt-6">

                        <h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">
                            Data yang akan diimport
                        </h3>

                        <div
                            class="overflow-x-auto rounded-xl border border-gray-200
                                   dark:border-gray-700"
                        >

                            <table class="w-full text-sm">

                                <thead class="bg-gray-50 dark:bg-gray-800">

                                    <tr>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            No.
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            Kegiatan
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            Bulan
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            PML
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            PCL
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            Satuan
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-right">
                                            Beban
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-left">
                                            Wilayah
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-right">
                                            Rate
                                        </th>

                                        <th class="whitespace-nowrap px-4 py-3 text-right">
                                            Honor Total
                                        </th>

                                    </tr>

                                </thead>

                                <tbody
                                    class="divide-y divide-gray-200 dark:divide-gray-700"
                                >

                                    @foreach ($preview as $index => $row)

                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">

                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $row['nama_kegiatan'] }}
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $row['bulan'] }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $row['nama_pml'] }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $row['nama_pcl'] }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $row['satuan'] }}
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                                {{ number_format($row['beban_banyak'], 0, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3">
                                                {{ $row['wilayah_tugas'] ?: '-' }}
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                                Rp {{ number_format($row['rate_honor'], 0, ',', '.') }}
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">
                                                Rp {{ number_format($row['honor_total'], 0, ',', '.') }}
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                @endif


                {{-- DUPLIKAT --}}
                @if (count($duplicates) > 0)

                    <div class="mt-6">

                        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-warning-700 dark:text-warning-400">

                            <x-heroicon-o-document-duplicate class="h-4 w-4" />

                            Data Duplikat

                        </h3>

                        <div class="space-y-2">

                            @foreach ($duplicates as $duplicate)

                                <div
                                    class="rounded-lg border border-warning-200 bg-warning-50 p-3 text-sm
                                           dark:border-warning-800 dark:bg-warning-950/30"
                                >

                                    <strong>
                                        Baris {{ $duplicate['row'] }}
                                    </strong>

                                    —
                                    {{ $duplicate['nama_kegiatan'] }}
                                    /
                                    {{ $duplicate['nama_pcl'] }}

                                    <span class="text-gray-600 dark:text-gray-400">
                                        ({{ $duplicate['reason'] }})
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- INVALID --}}
                @if (count($invalid) > 0)

                    <div class="mt-6">

                        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-danger-700 dark:text-danger-400">

                            <x-heroicon-o-exclamation-triangle class="h-4 w-4" />

                            Data Tidak Valid

                        </h3>

                        <div class="space-y-2">

                            @foreach ($invalid as $row)

                                <div
                                    class="rounded-lg border border-danger-200 bg-danger-50 p-3 text-sm
                                           dark:border-danger-800 dark:bg-danger-950/30"
                                >

                                    <strong>
                                        Baris {{ $row['row'] }}
                                    </strong>

                                    <ul class="mt-1 list-inside list-disc">

                                        @foreach ($row['errors'] as $error)

                                            <li>
                                                {{ $error }}
                                            </li>

                                        @endforeach

                                    </ul>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- ACTION --}}
                <div class="mt-6 flex justify-end gap-3">

                    <x-filament::button
                        color="gray"
                        wire:click="cancelImport"
                    >
                        Batal
                    </x-filament::button>

                    <x-filament::button
                        color="success"
                        icon="heroicon-o-arrow-up-tray"
                        wire:click="importData"
                        :disabled="count($preview) === 0"
                    >
                        Import {{ count($preview) }} Data
                    </x-filament::button>

                </div>

            </x-filament::section>

        @endif

    </div>

</x-filament-panels::page>