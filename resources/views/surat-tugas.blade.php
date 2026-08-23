<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Surat Tugas</title>

    <style>
        /*
        |--------------------------------------------------------------------------
        | DASAR
        |--------------------------------------------------------------------------
        */

        @page {
            margin: 2cm;
        }

        body {
            margin: 0;
            padding: 0;
            color: #000;
            font-family: "Cambria", Georgia, serif;
            font-size: 10pt;
            line-height: 1.4;
        }


        /*
        |--------------------------------------------------------------------------
        | SATU SURAT = SATU HALAMAN
        |--------------------------------------------------------------------------
        */

        .surat {
            width: 88%;
            margin: 0 auto;
            page-break-after: always;
        }

        .surat:last-child {
            page-break-after: auto;
        }


        /*
        |--------------------------------------------------------------------------
        | KOP SURAT
        |--------------------------------------------------------------------------
        */

        .kop {
            margin-bottom: 20px;
            text-align: center;
        }

        .logo {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
        }

        .kop-title {
            font-family: "Cambria", Georgia, serif;
            font-size: 11pt;
            font-weight: bold;
            font-style: italic;
            line-height: 1.25;
            text-align: center;
        }

        .kop-title .kabupaten {
            display: block;
        }


        /*
        |--------------------------------------------------------------------------
        | JUDUL SURAT
        |--------------------------------------------------------------------------
        */

        .judul {
            margin-bottom: 20px;
            font-family: "Cambria", Georgia, serif;
            font-size: 10pt;
            font-weight: normal;
            line-height: 1.4;
            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | TABEL
        |--------------------------------------------------------------------------
        */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .isi td {
            padding-bottom: 5px;
            vertical-align: top;
        }


        /*
        |--------------------------------------------------------------------------
        | LABEL
        |--------------------------------------------------------------------------
        |
        | Dibuat lebih lebar agar tanda ":" tidak terlalu dekat
        | dengan tulisan Menimbang, Mengingat, Kepada, Untuk,
        | Wilayah Tugas, dan Waktu.
        |
        */

        .label {
            width: 95px;
            padding-right: 8px;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | KOLOM TITIK DUA
        |--------------------------------------------------------------------------
        |
        | Lebar kolom dibuat sedikit lebih besar agar terdapat
        | jarak yang nyaman antara ":" dengan isi.
        |
        */

        .nomor {
            width: 25px;
            padding-right: 8px;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | ISI UTAMA
        |--------------------------------------------------------------------------
        */

        .isi-utama {
            text-align: justify;
            text-justify: inter-word;
        }


        /*
        |--------------------------------------------------------------------------
        | MENIMBANG
        |--------------------------------------------------------------------------
        */

        /*
        | Memberikan jarak tambahan setelah bagian Menimbang
        | sebelum masuk ke bagian Mengingat.
        */

        .baris-menimbang td {
            padding-bottom: 9px;
        }

        .menimbang-list {
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 22px;
            text-align: justify;
        }

        .menimbang-list li {
            margin-bottom: 3px;
            padding-left: 3px;
            text-align: justify;
            text-justify: inter-word;
        }

        .menimbang-single {
            margin: 0;
            text-align: justify;
            text-justify: inter-word;
        }


        /*
        |--------------------------------------------------------------------------
        | MENGINGAT
        |--------------------------------------------------------------------------
        */

        .mengingat-list {
            margin-top: 0;
            margin-bottom: 0;
            padding-left: 22px;
            text-align: justify;
        }

        .mengingat-list li {
            margin-bottom: 3px;
            padding-left: 3px;
            text-align: justify;
            text-justify: inter-word;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERI PERINTAH / MEMBERI TUGAS
        |--------------------------------------------------------------------------
        */

        .memberi-tugas {
            margin-top: 12px;
            margin-bottom: 10px;
            font-family: "Cambria", Georgia, serif;
            font-size: 10pt;
            font-weight: normal;
            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | DETAIL PENUGASAN
        |--------------------------------------------------------------------------
        */

        .detail-penugasan td {
            padding-bottom: 5px;
            vertical-align: top;
        }


        /*
        |--------------------------------------------------------------------------
        | TANDA TANGAN
        |--------------------------------------------------------------------------
        */

        .tanda-tangan {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .tanda-tangan td {
            vertical-align: top;
        }

        .tanggal {
            text-align: left;
            padding-left: 20px;
        }

        .nama-kepala {
            font-weight: bold;
            text-decoration: none;
            text-align: left;
        }
    </style>
</head>


<body>

    {{-- =========================================================
         PERULANGAN SURAT

         1 RECORD = 1 SURAT = 1 HALAMAN
    ========================================================== --}}

    @foreach ($suratTugas as $surat)

        <div class="surat">

            {{-- =================================================
                 KOP SURAT
            ================================================== --}}

            <div class="kop">

                <img
                    src="{{ public_path('images/logobps.png') }}"
                    class="logo"
                    alt="Logo BPS"
                >

                <div class="kop-title">

                    <span>
                        BADAN PUSAT STATISTIK
                    </span>

                    <span class="kabupaten">
                        KABUPATEN KEDIRI
                    </span>

                </div>

            </div>


            {{-- =================================================
                 JUDUL SURAT
            ================================================== --}}

            <div class="judul">

                SURAT PERINTAH/SURAT TUGAS

                <br>

                NOMOR: {{ $surat->nomor_surat }}

            </div>


            {{-- =================================================
                 MENIMBANG & MENGINGAT
            ================================================== --}}

            <table class="isi">

                {{-- =================================================
                     MENIMBANG
                ================================================== --}}

                <tr class="baris-menimbang">

                    <td class="label">
                        Menimbang
                    </td>

                    <td class="nomor">
                        :
                    </td>

                    <td>

                        @php

                            $menimbangItems = is_string($surat->menimbang)
                                ? json_decode($surat->menimbang, true)
                                : $surat->menimbang;

                            $menimbangItems = is_array($menimbangItems)
                                ? array_values(
                                    array_filter(
                                        $menimbangItems,
                                        fn ($item) =>
                                            is_array($item)
                                            && !empty($item['poin'])
                                    )
                                )
                                : [];

                        @endphp


                        @if (count($menimbangItems) === 1)

                            {{-- =================================================
                                 JIKA HANYA 1 POIN
                                 Tidak menggunakan nomor.
                            ================================================== --}}

                            <div class="menimbang-single">

                                {{ $menimbangItems[0]['poin'] }}

                            </div>


                        @elseif (count($menimbangItems) > 1)

                            {{-- =================================================
                                 JIKA LEBIH DARI 1 POIN
                                 Menggunakan nomor.
                            ================================================== --}}

                            <ol class="menimbang-list">

                                @foreach ($menimbangItems as $item)

                                    <li>
                                        {{ $item['poin'] }}
                                    </li>

                                @endforeach

                            </ol>


                        @else

                            -

                        @endif

                    </td>

                </tr>


                {{-- =================================================
                     MENGINGAT
                ================================================== --}}

                <tr>

                    <td class="label">
                        Mengingat
                    </td>

                    <td class="nomor">
                        :
                    </td>

                    <td>

                        @php

                            /*
                             * Model SuratTugas menggunakan:
                             *
                             * 'mengingat' => 'array'
                             *
                             * Pengecekan string tetap dipertahankan
                             * untuk mengantisipasi data lama.
                             */

                            $mengingatItems = is_string($surat->mengingat)
                                ? json_decode($surat->mengingat, true)
                                : $surat->mengingat;

                        @endphp


                        @if (
                            !empty($mengingatItems)
                            && is_array($mengingatItems)
                        )

                            <ol class="mengingat-list">

                                @foreach ($mengingatItems as $item)

                                    @if (
                                        is_array($item)
                                        && !empty($item['poin'])
                                    )

                                        <li>
                                            {{ $item['poin'] }}
                                        </li>

                                    @endif

                                @endforeach

                            </ol>

                        @else

                            -

                        @endif

                    </td>

                </tr>

            </table>


            {{-- =================================================
                 MEMBERI TUGAS
            ================================================== --}}

            <div class="memberi-tugas">

                Memberi Perintah/Memberi Tugas

            </div>


            {{-- =================================================
                 DETAIL PENUGASAN
            ================================================== --}}

            <table class="isi detail-penugasan">

                {{-- =================================================
                     KEPADA
                ================================================== --}}

                <tr>

                    <td class="label">
                        Kepada
                    </td>

                    <td class="nomor">
                        :
                    </td>

                    <td>
                        {{ $surat->nama_mitra ?? '-' }}
                    </td>

                </tr>


                {{-- =================================================
                     UNTUK
                ================================================== --}}

                <tr>

                    <td class="label">
                        Untuk
                    </td>

                    <td class="nomor">
                        :
                    </td>

                    <td class="isi-utama">

                        {{ $surat->untuk ?? '-' }}

                    </td>

                </tr>


                {{-- =================================================
                     WILAYAH TUGAS

                     Hanya Format 1 yang menampilkan wilayah tugas.
                     Letaknya setelah "Untuk".
                ================================================== --}}

                @if (
                    $surat->format_surat === 'format_1'
                    && !empty($surat->wilayah_tugas)
                )

                    <tr>

                        <td class="label">
                            Wilayah Tugas
                        </td>

                        <td class="nomor">
                            :
                        </td>

                        <td>
                            {{ $surat->wilayah_tugas }}
                        </td>

                    </tr>

                @endif


                {{-- =================================================
                     WAKTU
                ================================================== --}}

                <tr>

                    <td class="label">
                        Waktu
                    </td>

                    <td class="nomor">
                        :
                    </td>

                    <td>

                        @if (
                            $surat->tanggal_mulai
                            && $surat->tanggal_selesai
                        )

                            {{ $surat->tanggal_mulai
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                            s.d.

                            {{ $surat->tanggal_selesai
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                        @elseif ($surat->tanggal_mulai)

                            {{ $surat->tanggal_mulai
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                        @elseif ($surat->tanggal_selesai)

                            {{ $surat->tanggal_selesai
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                        @else

                            -

                        @endif

                    </td>

                </tr>

            </table>


            {{-- =================================================
                 TANDA TANGAN
            ================================================== --}}

            <table class="tanda-tangan">

                <tr>

                    <td width="60%">
                    </td>

                    <td width="40%" class="tanggal">

                        Kediri,

                        @if ($surat->tanggal_surat)

                            {{ $surat->tanggal_surat
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                        @else

                            -

                        @endif

                        <br>

                        Kepala BPS Kabupaten Kediri,

                        <br><br><br><br><br><br>

                        <div class="nama-kepala">

                            Bambang Indarto S.Si., M.Si

                        </div>

                    </td>

                </tr>

            </table>

        </div>

    @endforeach

</body>

</html>