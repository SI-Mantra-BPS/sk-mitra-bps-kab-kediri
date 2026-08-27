@php
    if (!function_exists('terbilang')) {
        function terbilang($angka) {
            $angka = abs((float)$angka);
            $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
            if ($angka < 12) return ' ' . $baca[(int)$angka];
            if ($angka < 20) return terbilang($angka - 10) . ' belas';
            if ($angka < 100) return terbilang($angka / 10) . ' puluh' . terbilang($angka % 10);
            if ($angka < 200) return ' seratus' . terbilang($angka - 100);
            if ($angka < 1000) return terbilang($angka / 100) . ' ratus' . terbilang($angka % 100);
            if ($angka < 2000) return ' seribu' . terbilang($angka - 1000);
            if ($angka < 1000000) return terbilang($angka / 1000) . ' ribu' . terbilang($angka % 1000);
            if ($angka < 1000000000) return terbilang($angka / 1000000) . ' juta' . terbilang($angka % 1000000);
            if ($angka < 1000000000000) return terbilang($angka / 1000000000) . ' miliar' . terbilang(fmod($angka, 1000000000));
            return trim(terbilang($angka / 1000000000000) . ' triliun' . terbilang(fmod($angka, 1000000000000)));
        }
    }

    // Helper aman untuk formatting tanggal dari berbagai format (DD/MM/YYYY, YYYY-MM-DD, Carbon, dsb)
    if (!function_exists('formatTanggalIndo')) {
        function formatTanggalIndo($val) {
            if (empty($val)) return '-';
            try {
                if (is_string($val) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($val))) {
                    return \Carbon\Carbon::createFromFormat('d/m/Y', trim($val))->locale('id')->isoFormat('D MMMM YYYY');
                }
                return \Carbon\Carbon::parse($val)->locale('id')->isoFormat('D MMMM YYYY');
            } catch (\Exception $e) {
                return $val;
            }
        }
    }

    $spkCollection = is_iterable($spkList ?? null) ? $spkList : collect([$spk ?? null])->filter();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Kerja</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.2;
            color: #000;
        }

        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

        /* Header Lembar Utama */
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 12px;
        }

        .table-pihak {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .table-pihak td {
            vertical-align: top;
            padding: 3px 0;
            line-height: 1.3;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 2px;
        }

        .pasal-content {
            text-align: justify;
            margin-bottom: 6px;
            text-indent: 18px;
        }

        .pasal-content-nolist {
            text-align: justify;
            margin-bottom: 6px;
        }

        /* Pager Break Helper */
        .page-break {
            page-break-after: always;
            clear: both;
        }

        /* ROTASI LAMPIRAN KE KANAN (90 DEGREE ROTATION) */
        .lampiran-page-break {
            page-break-before: always;
            clear: both;
        }

        .rotated-landscape-right {
            width: 245mm;  
            height: 165mm;
            transform: rotate(90deg);
            transform-origin: top left;
            margin-left: 175mm;
            margin-top: 0mm;
        }

        .header-bps-lampiran {
            text-align: center;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 12pt;
        }

        .table-title-lampiran {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        table.bps-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
        }

        table.bps-table th, 
        table.bps-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        table.bps-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Tabel TTD agar lebih stabil saat diprint massal */
        .table-ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-ttd td {
            vertical-align: top;
            text-align: center;
            width: 50%;
        }
    </style>
</head>
<body>

@foreach($spkCollection as $spkItem)
    @php
        // Ambil objek spk jika berbentuk Array/Model
        $item = is_array($spkItem) ? (object)$spkItem : $spkItem;

        $tglSpk = \Carbon\Carbon::parse($item->tanggal_spk ?? now())->locale('id');
        $tahunSpk = $tglSpk->format('Y');
        
        $details = $item->parsed_detail_kegiatan ?? (is_array($item->detail_kegiatan ?? null) 
            ? $item->detail_kegiatan 
            : json_decode($item->detail_kegiatan ?? '[]', true));
            
        $totalNilai = $item->total_nilai_perjanjian ?? 0;

        // PARSING TANGGAL PASAL 3 DENGAN MULTI-KEY FALLBACK
        $rawMulai = $item->tanggal_mulai ?? $item->tgl_mulai ?? $item->tanggal_mulai_perjanjian ?? null;
        $rawSelesai = $item->tanggal_selesai ?? $item->tgl_selesai ?? $item->tanggal_selesai_perjanjian ?? null;

        $tglMulai = formatTanggalIndo($rawMulai);
        $tglSelesai = formatTanggalIndo($rawSelesai);
    @endphp

    <!-- Container per 1 SPK -->
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        
        <!-- ========================================== -->
        <!-- LEMBAR UTAMA (SURAT PERJANJIAN)           -->
        <!-- ========================================== -->
        <div class="header-title">
            PERJANJIAN KERJA <br> PETUGAS PENCACAHAN/PENDATAAN LAPANGAN KEGIATAN SURVEI/SENSUS TAHUN {{ $tahunSpk }} <br> PADA BADAN PUSAT STATISTIK KABUPATEN KEDIRI<br>
            NOMOR: {{ $item->nomor_spk ?? '-' }}
        </div>

        <div class="pasal-content-nolist text-justify">
            Pada hari ini {{ $tglSpk->isoFormat('dddd') }}, 
            tanggal {{ $tglSpk->isoFormat('D') }}, 
            bulan {{ $tglSpk->isoFormat('MMMM') }}, 
            tahun {{ ucwords(terbilang($tahunSpk)) }}, bertempat di BPS KABUPATEN KEDIRI, yang bertanda tangan di bawah ini:
        </div>

        <table class="table-pihak">
            <tr>
                <td style="width: 3%;">1.</td>
                <td style="width: 32%; font-weight: bold;">{{ $item->nama_ppk ?? '-' }}</td>
                <td style="width: 2%;">:</td>
                <td style="width: 63%;" class="text-justify">
                    Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kediri, berkedudukan di Jl Pamenang No 42, Sukorejo, Ngasem, Kediri, bertindak untuk dan atas nama Badan Pusat Statistik Kabupaten Kediri, selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
                </td>
            </tr>
            <tr>
                <td>2.</td>
                <td style="font-weight: bold;">{{ $item->nama_pcl_formatted ?? ($item->pcl->nama_pcl ?? $item->nama_pcl ?? '-') }}</td>
                <td>:</td>
                <td class="text-justify">
                    Petugas Pendataan Lapangan Kegiatan Survei/Sensus Tahun {{ $tahunSpk }}, berkedudukan di {{ $item->alamat_pcl_formatted ?? ($item->alamat_pcl ?? '-') }}, bertindak untuk dan atas nama diri sendiri, selanjutnya disebut <strong>PIHAK KEDUA</strong>.
                </td>
            </tr>
        </table>

        <div class="pasal-content-nolist text-justify">
            bahwa <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> yang secara bersama-sama disebut <strong>PARA PIHAK</strong>, sepakat untuk mengikatkan diri dalam Perjanjian Kerja Petugas Kegiatan Survei/Sensus Tahun {{ $tahunSpk }} pada Badan Pusat Statistik Kabupaten Kediri, yang selanjutnya disebut Perjanjian, dengan ketentuan-ketentuan sebagai berikut:
        </div>

        <div class="pasal-title">Pasal 1</div>
        <div class="pasal-content">
            <strong>PIHAK PERTAMA</strong> memberikan pekerjaan kepada <strong>PIHAK KEDUA</strong> dan <strong>PIHAK KEDUA</strong> menerima pekerjaan dari <strong>PIHAK PERTAMA</strong> sebagai Petugas Pendataan Lapangan Kegiatan Survei/Sensus Tahun {{ $tahunSpk }} pada Badan Pusat Statistik Kabupaten Kediri, dengan lingkup pekerjaan yang ditetapkan oleh <strong>PIHAK PERTAMA</strong>.
        </div>

        <div class="pasal-title">Pasal 2</div>
        <div class="pasal-content">
            Ruang lingkup pekerjaan dalam Perjanjian ini mengacu pada wilayah kerja dan beban kerja sebagaimana tertuang dalam lampiran Perjanjian, Pedoman Petugas Pendataan Lapangan Kegiatan Survei/Sensus Tahun {{ $tahunSpk }} pada Badan Pusat Statistik Kabupaten Kediri, dan ketentuan-ketentuan yang ditetapkan oleh <strong>PIHAK PERTAMA</strong>.
        </div>

        <div class="pasal-title">Pasal 3</div>
        <div class="pasal-content">
            Jangka Waktu Perjanjian terhitung sejak tanggal {{ $tglMulai }} sampai dengan tanggal {{ $tglSelesai }}.
        </div>

        <div class="pasal-title">Pasal 4</div>
        <div class="pasal-content">
            <strong>PIHAK KEDUA</strong> berkewajiban melaksanakan seluruh pekerjaan yang diberikan oleh <strong>PIHAK PERTAMA</strong> sampai selesai, sesuai ruang lingkup pekerjaan sebagaimana dimaksud dalam Pasal 2, dengan menerapkan protokol kesehatan yang berlaku di wilayah kerja masing-masing.
        </div>

        <div class="pasal-title">Pasal 5</div>
        <div class="pasal-content">
            (1) <strong>PIHAK KEDUA</strong> berhak untuk mendapatkan honorarium petugas dari <strong>PIHAK PERTAMA</strong> sebesar Rp {{ number_format($totalNilai, 0, ',', '.') }},00 ({{ ucwords(terbilang($totalNilai)) }} Rupiah) untuk pekerjaan sebagaimana dimaksud dalam Pasal 2, termasuk biaya pajak, bea materai, dan jasa pelayanan keuangan.
        </div>
        <div class="pasal-content">
            (2) Selain honorarium sebagaimana dimaksud pada ayat (1), <strong>PIHAK KEDUA</strong> dapat diberikan paket data dan komunikasi selama pelaksanaan pekerjaan sesuai dengan ketentuan yang berlaku di <strong>PIHAK PERTAMA</strong> dan ketentuan peraturan perundang-undangan.
        </div>
        <div class="pasal-content">
            (3) <strong>PIHAK KEDUA</strong> tidak diberikan honorarium tambahan apabila melakukan kunjungan di luar jadwal atau terdapat tambahan waktu pelaksanaan pekerjaan lapangan.
        </div>

        <div class="pasal-title">Pasal 6</div>
        <div class="pasal-content">
            (1) Pembayaran honorarium sebagaimana dimaksud dalam Pasal 5 dilakukan setelah <strong>PIHAK KEDUA</strong> menyelesaikan dan menyerahkan seluruh hasil pekerjaan sebagaimana dimaksud dalam Pasal 2 kepada <strong>PIHAK PERTAMA</strong>.
        </div>
        <div class="pasal-content">
            (2) Pembayaran sebagaimana dimaksud pada ayat (1) dilakukan oleh <strong>PIHAK PERTAMA</strong> kepada <strong>PIHAK KEDUA</strong> sesuai dengan ketentuan peraturan perundang-undangan.
        </div>

        <div class="pasal-title">Pasal 7</div>
        <div class="pasal-content">
            Penyerahan hasil pekerjaan lapangan sebagaimana dimaksud dalam Pasal 2 dilakukan secara bertahap dan selambat-lambatnya seluruh hasil pekerjaan lapangan diserahkan sesuai jadwal yang tercantum dalam Lampiran, yang dinyatakan dalam Berita Acara Serah Terima Hasil Pekerjaan yang ditandatangani oleh <strong>PARA PIHAK</strong>.
        </div>

        <div class="pasal-title">Pasal 8</div>
        <div class="pasal-content">
            <strong>PIHAK PERTAMA</strong> dapat memutuskan Perjanjian ini secara sepihak sewaktu-waktu dalam hal <strong>PIHAK KEDUA</strong> tidak dapat melaksanakan kewajibannya sebagaimana dimaksud dalam Pasal 4, dengan menerbitkan Surat Pemutusan Perjanjian Kerja.
        </div>

        <div class="pasal-title">Pasal 9</div>
        <div class="pasal-content">
            Dalam hal <strong>PIHAK KEDUA</strong> meninggal dunia, mengundurkan diri karena sakit dengan keterangan rawat inap, kecelakaan dengan keterangan kepolisian, dan/atau telah diberikan Surat Pemutusan Perjanjian Kerja dari <strong>PIHAK PERTAMA</strong>, maka <strong>PIHAK PERTAMA</strong> membayarkan honorarium kepada <strong>PIHAK KEDUA</strong> secara proporsional sesuai pekerjaan yang telah dilaksanakan.
        </div>

        <div class="pasal-title">Pasal 10</div>
        <div class="pasal-content">
            (1) Apabila terjadi Keadaan Kahar, yang meliputi bencana alam dan bencana sosial, <strong>PIHAK KEDUA</strong> memberitahukan kepada <strong>PIHAK PERTAMA</strong> dalam waktu paling lambat 7 (tujuh) hari sejak mengetahui atas kejadian Keadaan Kahar dengan menyertakan bukti.
        </div>
        <div class="pasal-content">
            (2) Pada saat terjadi Keadaan Kahar, pelaksanaan pekerjaan oleh <strong>PIHAK KEDUA</strong> dihentikan sementara dan dilanjutkan kembali setelah Keadaan Kahar berakhir, namun apabila akibat Keadaan Kahar tidak memungkinkan dilanjutkan/diselesaikannya pelaksanaan pekerjaan, <strong>PIHAK KEDUA</strong> berhak menerima honorarium secara proporsional sesuai pekerjaan yang telah dilaksanakan.
        </div>

        <div class="pasal-title">Pasal 11</div>
        <div class="pasal-content">
            Segala sesuatu yang belum atau tidak cukup diatur dalam Perjanjian ini, dituangkan dalam perjanjian tambahan/<i>addendum</i> dan merupakan bagian tidak terpisahkan dari perjanjian ini.
        </div>

        <div class="pasal-title">Pasal 12</div>
        <div class="pasal-content">
            (1) Segala perselisihan atau perbedaan pendapat yang timbul sebagai akibat adanya Perjanjian ini akan diselesaikan secara musyawarah untuk mufakat.
        </div>
        <div class="pasal-content">
            (2) Apabila musyawarah untuk mufakat sebagaimana dimaksud pada ayat (1) tidak berhasil, maka <strong>PARA PIHAK</strong> sepakat untuk menyelesaikan perselisihan dengan memilih kedudukan/domisili hukum di Kepaniteraan Pengadilan Negeri.
        </div>
        <div class="pasal-content">
            (3) Selama perselisihan dalam proses penyelesaian pengadilan, <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> wajib tetap melaksanakan kewajiban masing-masing berdasarkan Perjanjian ini.
        </div>

        <div class="pasal-content-nolist text-justify" style="margin-top: 10px;">
            Demikian Perjanjian ini dibuat dan ditandatangani oleh <strong>PARA PIHAK</strong> dalam 2 (dua) rangkap asli bermeterai cukup, tanpa paksaan dari <strong>PIHAK</strong> manapun dan untuk dilaksanakan oleh <strong>PARA PIHAK</strong>.
        </div>

        <table class="table-ttd">
            <tr>
                <td>
                    <p><strong>PIHAK KEDUA</strong>,</p>
                    <!-- Ubah margin: 10px 0; menjadi nilai yang lebih besar, misal 60px 0; -->
                    <div style="font-size: 8pt; color: #777; margin: 60px 0 10px 0;">[Materai 10.000]</div>
                    <p><strong><u>{{ $item->nama_pcl_formatted ?? ($item->pcl->nama_pcl ?? $item->nama_pcl ?? '-') }}</u></strong></p>
                </td>
                <td>
                    <p><strong>PIHAK PERTAMA</strong>,</p>
                    <!-- Ubah margin: 10px 0; menjadi nilai yang sama, misal 60px 0; -->
                    <div style="font-size: 8pt; color: #fff; margin: 60px 0 10px 0;">&nbsp;</div>
                    <p><strong><u>{{ $item->nama_ppk ?? '-' }}</u></strong></p>
                </td>
            </tr>
        </table>

        <!-- ========================================== -->
        <!-- LEMBAR LAMPIRAN (ROTASI KANAN & JUDUL TENGAH) -->
        <!-- ========================================== -->
        <div class="lampiran-page-break">
            <div class="rotated-landscape-right">
                <div class="header-bps-lampiran">
                    <div class="uppercase">LAMPIRAN</div>
                    <div>PERJANJIAN KERJA PETUGAS PENCACAHAN/PENDATAAN</div>
                    <div>LAPANGAN KEGIATAN SURVEI/SENSUS TAHUN {{ $tahunSpk }} PADA BADAN</div>
                    <div>PUSAT STATISTIK KABUPATEN KEDIRI</div>
                    <div>NOMOR: {{ $item->nomor_spk ?? '-' }}</div>
                </div>

                <div class="table-title-lampiran">
                    DAFTAR URAIAN TUGAS, JANGKA WAKTU, NILAI PERJANJIAN, DAN BEBAN ANGGARAN
                </div>

                <table class="bps-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 4%;">No</th>
                            <th rowspan="2" style="width: 32%;">Uraian Tugas</th>
                            <th rowspan="2" style="width: 16%;">Jangka Waktu</th>
                            <th colspan="2" style="width: 14%;">Target Pekerjaan</th>
                            <th rowspan="2" style="width: 10%;">Harga Satuan</th>
                            <th rowspan="2" style="width: 10%;">Nilai Perjanjian</th>
                            <th rowspan="2" style="width: 14%;">Beban Anggaran</th>
                        </tr>
                        <tr>
                            <th style="width: 5%;">Volume</th>
                            <th style="width: 9%;">Satuan</th>
                        </tr>
                        <tr>
                            <th>(1)</th>
                            <th>(2)</th>
                            <th>(3)</th>
                            <th>(4)</th>
                            <th>(5)</th>
                            <th>(6)</th>
                            <th>(7)</th>
                            <th>(8)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $calculatedTotal = 0; @endphp
                        @if(!empty($details) && count($details) > 0)
                            @foreach ($details as $index => $row)
                                @php
                                    $vol = $row['volume'] ?? 0;
                                    $harga = $row['harga_satuan'] ?? 0;
                                    $nilai = $row['nilai_perjanjian'] ?? ($vol * $harga);
                                    $calculatedTotal += $nilai;
                                    
                                    $jangkaWaktuText = $row['jangka_waktu_text'] ?? ($tglMulai . ' sd ' . $tglSelesai);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row['uraian_tugas'] ?? '-' }}</td>
                                    <td class="text-center">{{ $jangkaWaktuText }}</td>
                                    <td class="text-center">{{ $vol }}</td>
                                    <td class="text-center">{{ $row['satuan'] ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($harga, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($nilai, 0, ',', '.') }}</td>
                                    <td class="text-center" style="font-size: 8pt;">{{ $row['beban_anggaran'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="italic font-bold text-right">Total:</td>
                            <td class="text-right font-bold">{{ number_format($calculatedTotal > 0 ? $calculatedTotal : $totalNilai, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" class="italic">
                                <strong>Terbilang:</strong> {{ ucwords(terbilang($calculatedTotal > 0 ? $calculatedTotal : $totalNilai)) }} Rupiah
                            </td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>

    </div>
@endforeach

</body>
</html>