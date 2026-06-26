<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LKE ZI - {{ $opd->n_opd }} ({{ $periode->tahun }})</title>
    <style>
        @page {
            margin: 50px 35px 50px 35px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0E7C7B;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            color: #0E7C7B;
            text-transform: uppercase;
            margin: 0 0 3px 0;
        }
        .header-subtitle {
            font-size: 11px;
            color: #555;
            margin: 0 0 5px 0;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 9px;
            border: none;
        }
        .info-table td.label {
            font-weight: bold;
            color: #4b5563;
            width: 120px;
        }
        .info-table td.value {
            color: #1f2937;
        }

        .komponen-title {
            background-color: #0E7C7B;
            color: #ffffff;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 8px;
            border-radius: 3px;
        }
        .kategori-title {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 4px 6px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 6px;
            border-left: 3px solid #0E7C7B;
        }
        .subkategori-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0c6665;
            margin-top: 8px;
            margin-bottom: 4px;
            padding-left: 4px;
        }

        .indicator-block {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .indicator-header {
            background-color: #f9fafb;
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: bold;
            color: #374151;
        }
        .indicator-stats {
            float: right;
            font-size: 8.5px;
            color: #0E7C7B;
        }

        .pertanyaan-table {
            width: 100%;
            border-collapse: collapse;
        }
        .pertanyaan-table th, .pertanyaan-table td {
            border-bottom: 1px solid #f3f4f6;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }
        .pertanyaan-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }
        .pertanyaan-row:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-score {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-weight {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .sub-pertanyaan-list {
            margin-top: 4px;
            padding-left: 15px;
            list-style-type: none;
        }
        .sub-pertanyaan-item {
            margin-bottom: 3px;
            color: #4b5563;
        }

        .doc-link {
            color: #2563eb;
            text-decoration: none;
        }
        .doc-link:hover {
            text-decoration: underline;
        }
        .keterangan-text {
            font-style: italic;
            color: #6b7280;
            margin-top: 3px;
        }
        .footer {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 7.5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
        .footer .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="footer">
        Halaman <span class="page-number"></span> | LKE Zona Integritas - Pemerintah Kabupaten/Kota | Dicetak otomatis pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>

    <div class="header">
        <div class="header-title">Lembar Kerja Evaluasi (LKE) Zona Integritas</div>
        <div class="header-subtitle">Detail Jawaban dan Nilai Evaluasi WBK / WBBM</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Unit Kerja (OPD)</td>
            <td class="value">: {{ $opd->n_opd }}</td>
            <td class="label">Periode Evaluasi</td>
            <td class="value">: {{ $periode->nama_periode }} ({{ $periode->tahun }})</td>
        </tr>
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td class="value">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</td>
            <td class="label">Status Verifikasi</td>
            <td class="value">: 
                @php
                    $isSent = \Illuminate\Support\Facades\DB::table('jawaban')
                        ->where('periode_id', $periode->id)
                        ->where('opd_id', $opd->id)
                        ->where('status', 'final')
                        ->exists();
                @endphp
                {{ $isSent ? 'Terkirim ke Verifikator (Final)' : 'Draft / Sedang Mengisi' }}
            </td>
        </tr>
    </table>

    @foreach ($komponens as $komponen)
        <div class="komponen-title">
            {{ $komponen->kode }}. {{ $komponen->nama }}
        </div>

        @foreach ($komponen->kategoris as $kategori)
            <div class="kategori-title">
                {{ $kategori->kode }}. {{ $kategori->nama }}
            </div>

            @foreach ($kategori->subKategoris as $subKategori)
                <div class="subkategori-title">
                    {{ $subKategori->kode }}. {{ $subKategori->nama }}
                    <span style="font-weight: normal; color: #4b5563; font-size: 8.5px;">
                        (Nilai Sub Kategori: {{ number_format($progress[$subKategori->id] ?? 0, 2) }})
                    </span>
                </div>

                @foreach ($subKategori->indikators as $indikator)
                    @php
                        $indikatorData = $indikatorResults[$indikator->id] ?? [
                            'rata_rata_nilai' => 0,
                            'nilai_indikator' => 0,
                            'nilai_per_pertanyaan' => []
                        ];
                    @endphp
                    <div class="indicator-block">
                        <div class="indicator-header">
                            <span class="indicator-stats">
                                Bobot: {{ $indikator->bobot }} | Nilai: {{ number_format($indikatorData['nilai_indikator'], 2) }}
                            </span>
                            {{ $indikator->kode }}. {{ $indikator->nama }}
                        </div>

                        <table class="pertanyaan-table">
                            <thead>
                                <tr>
                                    <th style="width: 55%;">Pertanyaan / Pilihan Jawaban</th>
                                    <th style="width: 18%;">Jawaban Operator</th>
                                    <th style="width: 18%;">Jawaban Verifikator</th>
                                    <th style="width: 9%; text-align: right;">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indikator->pertanyaans as $pIndex => $pertanyaan)
                                    @php
                                        $jParent = $jawabanMap[$pertanyaan->id] ?? null;
                                        $pNilaiData = $indikatorData['nilai_per_pertanyaan'][$pertanyaan->id] ?? null;
                                        $pNilai = ($pNilaiData && $pNilaiData['nilai'] !== null) ? $pNilaiData['nilai'] : null;
                                    @endphp
                                    <tr class="pertanyaan-row">
                                        <td>
                                            <strong>{{ $pertanyaan->urutan ?? ($pIndex + 1) }}.</strong> {{ $pertanyaan->pertanyaan }}
                                            
                                            @if ($pertanyaan->has_sub_pertanyaan)
                                                <ul class="sub-pertanyaan-list">
                                                    @foreach ($pertanyaan->subPertanyaans as $sp)
                                                        @php
                                                            $spKey = "{$pertanyaan->id}_{$sp->id}";
                                                            $jSub = $jawabanMap[$spKey] ?? null;
                                                        @endphp
                                                        <li class="sub-pertanyaan-item">
                                                            - {{ $sp->pertanyaan }}:
                                                            <span style="color: #111827; font-weight: 500;">
                                                                (Opr: {{ $jSub->jawaban_angka ?? '-' }} | Verif: {{ $jSub->verifikator_jawaban_angka ?? $jSub->jawaban_angka ?? '-' }}) {{ $sp->satuan }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if ($pertanyaan->formula)
                                                    <div style="font-size: 7.5px; color: #6b7280; margin-top: 3px;">
                                                        <strong>Formula:</strong> {{ $pertanyaan->formula }}
                                                    </div>
                                                @endif
                                            @else
                                                {{-- Tampilkan Opsi Pilihan Ganda / Ya-Tidak --}}
                                                @if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda']) && !empty($pertanyaan->penjelasan_list))
                                                    <div style="font-size: 7.5px; color: #6b7280; margin-top: 3px; line-height: 1.2;">
                                                        @foreach ($pertanyaan->penjelasan_list as $opsi)
                                                            <strong>{{ $opsi['opsi'] ?? '' }}:</strong> {{ $opsi['text'] ?? '' }}<br/>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$pertanyaan->has_sub_pertanyaan)
                                                @if ($pertanyaan->tipe_jawaban === 'angka')
                                                    {{ $jParent->jawaban_angka ?? '-' }}
                                                @else
                                                    {{ $jParent->jawaban_text ?? '-' }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$pertanyaan->has_sub_pertanyaan)
                                                @if ($pertanyaan->tipe_jawaban === 'angka')
                                                    {{ $jParent->verifikator_jawaban_angka ?? $jParent->jawaban_angka ?? '-' }}
                                                @else
                                                    {{ $jParent->verifikator_jawaban_text ?? $jParent->jawaban_text ?? '-' }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="text-align: right; font-weight: bold;">
                                            @if ($pNilai !== null)
                                                <span class="badge badge-score">
                                                    {{ $pertanyaan->has_sub_pertanyaan ? number_format($pNilai * 100, 2) . '%' : number_format($pNilai, 2) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endforeach
        @endforeach
    @endforeach

</body>
</html>
