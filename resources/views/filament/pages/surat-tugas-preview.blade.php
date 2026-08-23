<div
    style="
        width: 100%;
        background: #f3f4f6;
        padding: 25px;
        box-sizing: border-box;
        overflow-y: auto;
    "
>
    {{-- =========================================================
         KERTAS SURAT
    ========================================================== --}}
    <div
        style="
            width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            padding: 65px 75px;
            box-sizing: border-box;

            background: #ffffff;
            color: #000000;

            font-family: Cambria, Georgia, serif;
            font-size: 10pt;
            line-height: 1.4;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.15);
        "
    >

        {{-- =====================================================
             KOP SURAT
        ====================================================== --}}
        <div
            style="
                text-align: center;
                margin-bottom: 20px;
            "
        >

            <img
                src="{{ asset('images/logobps.png') }}"
                alt="Logo BPS"
                style="
                    width: 70px;
                    height: auto;
                    display: block;
                    margin: 0 auto 10px auto;
                "
            >

            <div
                style="
                    font-family: Cambria, Georgia, serif;
                    font-size: 11pt;
                    font-weight: bold;
                    font-style: italic;
                    line-height: 1.25;
                "
            >
                BADAN PUSAT STATISTIK
                <br>
                KABUPATEN KEDIRI
            </div>

        </div>


        {{-- =====================================================
             JUDUL SURAT
        ====================================================== --}}
        <div
            style="
                margin-bottom: 20px;
                font-family: Cambria, Georgia, serif;
                font-size: 10pt;
                line-height: 1.4;
                text-align: center;
            "
        >

            SURAT PERINTAH/SURAT TUGAS

            <br>

            NOMOR: {{ $surat->nomor_surat }}

        </div>


        {{-- =====================================================
             ISI SURAT
        ====================================================== --}}
        <table
            style="
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            "
        >

            {{-- =================================================
                 MENIMBANG
            ================================================== --}}
            <tr>

                <td
                    style="
                        width: 95px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                        padding-bottom: 9px;
                    "
                >
                    Menimbang
                </td>

                <td
                    style="
                        width: 25px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                        padding-bottom: 9px;
                    "
                >
                    :
                </td>

                <td
                    style="
                        vertical-align: top;
                        padding-bottom: 9px;
                        text-align: justify;
                    "
                >

                    @php
                        $menimbangItems = is_string($surat->menimbang)
                            ? json_decode($surat->menimbang, true)
                            : $surat->menimbang;
                    @endphp

                    @if (
                        !empty($menimbangItems)
                        && is_array($menimbangItems)
                    )

                        @php
                            // Hanya ambil item yang memiliki poin
                            $validMenimbangItems = collect($menimbangItems)
                                ->filter(
                                    fn ($item) =>
                                        is_array($item)
                                        && !empty($item['poin'])
                                )
                                ->values();
                        @endphp

                        @if ($validMenimbangItems->count() === 1)

                            {{-- SATU POIN: TANPA NOMOR --}}
                            <div
                                style="
                                    text-align: justify;
                                "
                            >
                                {{ $validMenimbangItems->first()['poin'] }}
                            </div>

                        @elseif ($validMenimbangItems->count() > 1)

                            {{-- LEBIH DARI SATU POIN: PAKAI NOMOR --}}
                            @foreach ($validMenimbangItems as $item)

                                <div
                                    style="
                                        display: table;
                                        width: 100%;
                                        margin-bottom: 3px;
                                        text-align: justify;
                                    "
                                >

                                    {{-- NOMOR --}}
                                    <div
                                        style="
                                            display: table-cell;
                                            width: 22px;
                                            padding-right: 4px;
                                            vertical-align: top;
                                        "
                                    >
                                        {{ $loop->iteration }}.
                                    </div>

                                    {{-- ISI --}}
                                    <div
                                        style="
                                            display: table-cell;
                                            vertical-align: top;
                                            text-align: justify;
                                        "
                                    >
                                        {{ $item['poin'] }}
                                    </div>

                                </div>

                            @endforeach

                        @else

                            -

                        @endif

                    @else

                        -

                    @endif

                </td>

            </tr>


            {{-- =================================================
                 MENGINGAT
            ================================================== --}}
            <tr>

                <td
                    style="
                        width: 95px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    Mengingat
                </td>

                <td
                    style="
                        width: 25px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    :
                </td>

                <td
                    style="
                        vertical-align: top;
                        text-align: justify;
                    "
                >

                    @php
                        $mengingatItems = is_string($surat->mengingat)
                            ? json_decode($surat->mengingat, true)
                            : $surat->mengingat;
                    @endphp

                    @if (
                        !empty($mengingatItems)
                        && is_array($mengingatItems)
                    )

                        <div
                            style="
                                margin-top: 0;
                                margin-bottom: 0;
                            "
                        >

                            @foreach ($mengingatItems as $item)

                                @if (
                                    is_array($item)
                                    && !empty($item['poin'])
                                )

                                    <div
                                        style="
                                            display: table;
                                            width: 100%;
                                            margin-bottom: 3px;
                                            text-align: justify;
                                        "
                                    >

                                        {{-- NOMOR --}}
                                        <div
                                            style="
                                                display: table-cell;
                                                width: 22px;
                                                padding-right: 4px;
                                                vertical-align: top;
                                            "
                                        >
                                            {{ $loop->iteration }}.
                                        </div>

                                        {{-- ISI DASAR HUKUM --}}
                                        <div
                                            style="
                                                display: table-cell;
                                                vertical-align: top;
                                                text-align: justify;
                                            "
                                        >
                                            {{ $item['poin'] }}
                                        </div>

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    @else

                        -

                    @endif

                </td>

            </tr>

        </table>


        {{-- =====================================================
             MEMBERI TUGAS
        ====================================================== --}}
        <div
            style="
                margin-top: 12px;
                margin-bottom: 10px;

                font-family: Cambria, Georgia, serif;
                font-size: 10pt;
                font-weight: normal;

                text-align: center;
            "
        >
            Memberi Perintah/Memberi Tugas
        </div>


        {{-- =====================================================
             DETAIL PENUGASAN
        ====================================================== --}}
        <table
            style="
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            "
        >

            {{-- KEPADA --}}
            <tr>

                <td
                    style="
                        width: 95px;
                        padding-right: 8px;
                        padding-bottom: 5px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    Kepada
                </td>

                <td
                    style="
                        width: 25px;
                        padding-right: 8px;
                        padding-bottom: 5px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    :
                </td>

                <td
                    style="
                        padding-bottom: 5px;
                        vertical-align: top;
                    "
                >
                    {{ $surat->nama_mitra ?? '-' }}
                </td>

            </tr>


            {{-- UNTUK --}}
            <tr>

                <td
                    style="
                        width: 95px;
                        padding-right: 8px;
                        padding-bottom: 5px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    Untuk
                </td>

                <td
                    style="
                        width: 25px;
                        padding-right: 8px;
                        padding-bottom: 5px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    :
                </td>

                <td
                    style="
                        padding-bottom: 5px;
                        vertical-align: top;
                        text-align: justify;
                    "
                >
                    {{ $surat->untuk ?? '-' }}
                </td>

            </tr>


            {{-- WILAYAH TUGAS --}}
            @if (
                $surat->format_surat === 'format_1'
                && !empty($surat->wilayah_tugas)
            )

                <tr>

                    <td
                        style="
                            width: 95px;
                            padding-right: 8px;
                            padding-bottom: 5px;
                            vertical-align: top;
                            white-space: nowrap;
                        "
                    >
                        Wilayah Tugas
                    </td>

                    <td
                        style="
                            width: 25px;
                            padding-right: 8px;
                            padding-bottom: 5px;
                            vertical-align: top;
                            white-space: nowrap;
                        "
                    >
                        :
                    </td>

                    <td
                        style="
                            padding-bottom: 5px;
                            vertical-align: top;
                        "
                    >
                        {{ $surat->wilayah_tugas }}
                    </td>

                </tr>

            @endif


            {{-- WAKTU --}}
            <tr>

                <td
                    style="
                        width: 95px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    Waktu
                </td>

                <td
                    style="
                        width: 25px;
                        padding-right: 8px;
                        vertical-align: top;
                        white-space: nowrap;
                    "
                >
                    :
                </td>

                <td
                    style="
                        vertical-align: top;
                    "
                >

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


        {{-- =====================================================
             TANDA TANGAN
        ====================================================== --}}
        <table
            style="
                width: 100%;
                border-collapse: collapse;
                margin-top: 45px;
                page-break-inside: avoid;
            "
        >

            <tr>

                {{-- KOSONG --}}
                <td
                    style="
                        width: 60%;
                        vertical-align: top;
                    "
                >
                </td>


                {{-- TTD --}}
                <td
                    style="
                        width: 40%;
                        vertical-align: top;
                        text-align: left;
                        padding-left: 20px;
                    "
                >

                    <div>

                        Kediri,

                        @if ($surat->tanggal_surat)

                            {{ $surat->tanggal_surat
                                ->locale('id')
                                ->translatedFormat('d F Y') }}

                        @else

                            -

                        @endif

                    </div>

                    <div>
                        Kepala BPS Kabupaten Kediri,
                    </div>


                    {{-- RUANG TANDA TANGAN --}}
                    <div
                        style="
                            height: 105px;
                        "
                    ></div>


                    <div
                        style="
                            font-weight: bold;
                        "
                    >
                        Bambang Indarto S.Si., M.Si
                    </div>

                </td>

            </tr>

        </table>

    </div>

</div>