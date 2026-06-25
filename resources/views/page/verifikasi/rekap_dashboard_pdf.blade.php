<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Hasil Evaluasi ZI - {{ $activePeriode->nama_periode }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #333;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #0164CA;
            padding-bottom: 8px;
        }
        .header h1 {
            margin: 0 0 4px 0;
            color: #0164CA;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 11px;
            color: #555;
            font-weight: normal;
        }
        
        .info-container {
            width: 100%;
            margin-bottom: 12px;
        }
        .info-table {
            border: none;
            width: auto;
        }
        .info-table td {
            padding: 2px 4px;
            font-size: 9px;
            border: none;
        }
        .info-table td.label {
            font-weight: bold;
            color: #555;
            width: 90px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #c8d7e6;
            padding: 4px 3px;
            vertical-align: middle;
            text-align: center;
        }
        table.data-table th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5px;
        }
        table.data-table thead tr.main-header th {
            background-color: #0164CA;
            color: #ffffff;
            border: 1px solid #0150A8;
        }
        table.data-table thead tr.sub-header th {
            background-color: #0257B2;
            color: #ffffff;
            font-size: 7px;
            border: 1px solid #01418F;
        }
        
        /* Ambang Batas Row styling */
        .threshold-row {
            background-color: #FFFbeb !important;
            font-weight: bold;
            color: #92400E;
        }
        .threshold-row td {
            border: 1px solid #FDE68A !important;
        }

        /* Value styling */
        .val-num {
            font-weight: bold;
            font-size: 8.5px;
            color: #111827;
        }
        .val-pct {
            font-size: 7px;
            color: #6B7280;
            margin-top: 1px;
        }
        
        /* Non-compliance cells */
        .failed-cell {
            background-color: #FEE2E2 !important;
        }
        .failed-cell .val-num {
            color: #B91C1C !important;
        }
        .failed-cell .val-pct {
            color: #EF4444 !important;
            font-weight: bold;
        }

        /* Opd Name Left Align */
        .opd-name {
            text-align: left !important;
            font-weight: bold;
            color: #1F2937;
            max-width: 150px;
            word-wrap: break-word;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 9999px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .border-r-double {
            border-right: 2px solid #0150A8 !important;
        }
        .border-r-bold {
            border-right: 1.5px solid #4B5563 !important;
        }
        .border-r-light {
            border-right: 1.5px solid #9CA3AF !important;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rekapan Hasil Evaluasi Zona Integritas</h1>
        <h2>Wilayah Bebas dari Korupsi (WBK) / Wilayah Birokrasi Bersih dan Melayani (WBBM)</h2>
    </div>

    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="label">Periode Evaluasi</td>
                <td>: {{ $activePeriode->nama_periode }} ({{ $activePeriode->tahun }})</td>
            </tr>
            <tr>
                <td class="label">Jenis Penilaian</td>
                <td>: 
                    @if($role == 'operator')
                        Nilai Mandiri Unit Kerja (Internal)
                    @elseif($role == 'verifikator')
                        Nilai Tim Penilai Internal (TPI)
                    @else
                        Nilai Tim Penilai Evaluasi (TPE)
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal Ekspor</td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr class="main-header">
                <th rowspan="2" style="width: 3%;" class="border-r-light">No</th>
                <th rowspan="2" style="width: 17%;" class="border-r-bold">Unit Kerja</th>
                <th colspan="7" style="width: 45%;" class="border-r-bold">Pengungkit ({{ number_format($bobotMeta['pengungkit_total'], 2) }}%)</th>
                <th colspan="4" style="width: 25%;" class="border-r-bold">Hasil ({{ number_format($bobotMeta['hasil_total'], 2) }}%)</th>
                <th rowspan="2" style="width: 5%;" class="border-r-bold">Total (100%)</th>
                <th rowspan="2" style="width: 5%;">Simpulan</th>
            </tr>
            <tr class="sub-header">
                @foreach ($areaOrder as $areaName)
                    <th style="width: 6%;" class="border-r-light">
                        {{ $areaName }}
                        <div style="font-size: 5.5px; opacity: 0.85;">({{ number_format($bobotMeta['area'][$areaName], 2) }}%)</div>
                    </th>
                @endforeach
                <th style="width: 9%;" class="border-r-bold">Jml Pengungkit</th>
                <th style="width: 6%;" class="border-r-light">SPAK<br><div style="font-size: 5.5px; opacity: 0.85;">({{ number_format($bobotMeta['hasil']['spak'], 2) }}%)</div></th>
                <th style="width: 6%;" class="border-r-light">Capaian<br><div style="font-size: 5.5px; opacity: 0.85;">({{ number_format($bobotMeta['hasil']['capaian'], 2) }}%)</div></th>
                <th style="width: 6%;" class="border-r-light">Birokrasi Bersih<br><div style="font-size: 5.5px; opacity: 0.85;">({{ number_format($bobotMeta['hasil']['birokrasi'], 2) }}%)</div></th>
                <th style="width: 7%;" class="border-r-bold">SPP / Pelayanan<br><div style="font-size: 5.5px; opacity: 0.85;">({{ number_format($bobotMeta['hasil']['pelayanan'], 2) }}%)</div></th>
            </tr>
        </thead>
        <tbody>
            <!-- Threshold / Ambang Batas Row -->
            <tr class="threshold-row">
                <td colspan="2" class="opd-name border-r-bold" style="font-weight: bold; color: #92400E; text-align: center !important;">Ambang Batas WBK</td>
                @foreach ($areaOrder as $areaName)
                    <td class="border-r-light">&ge; {{ number_format($thresholds['area'][$areaName], 2) }}</td>
                @endforeach
                <td class="border-r-bold">&ge; {{ number_format($thresholds['pengungkit_total'], 2) }}</td>
                <td class="border-r-light">&ge; {{ number_format($thresholds['spak'], 2) }}</td>
                <td class="border-r-light">&ge; {{ number_format($thresholds['capaian'], 2) }}</td>
                <td class="border-r-light">&ge; 18.25</td>
                <td class="border-r-bold">&ge; {{ number_format($thresholds['pelayanan'], 2) }}</td>
                <td class="border-r-bold">&ge; {{ number_format($thresholds['total'], 2) }}</td>
                <td>-</td>
            </tr>

            @if ($rekapRows->isEmpty())
                <tr>
                    <td colspan="15" style="text-align: center; padding: 15px; font-style: italic; color: #666; font-size: 9px;">
                        Belum ada data rekapan hasil verifikasi untuk periode ini.
                    </td>
                </tr>
            @else
                @foreach ($rekapRows as $index => $row)
                    <tr>
                        <td class="border-r-light">{{ $index + 1 }}</td>
                        <td class="opd-name border-r-bold">{{ $row['opd'] }}</td>
                        
                        <!-- Areas -->
                        @foreach ($row['areas'] as $area)
                            @php
                                $areaName = $area['nama'];
                                $complianceArea = $row['compliance']['areas'][$areaName] ?? null;
                                $isAreaPassed = $complianceArea ? $complianceArea['is_passed'] : true;
                            @endphp
                            <td class="border-r-light {{ !$isAreaPassed ? 'failed-cell' : '' }}">
                                <div class="val-num">{{ number_format($area['nilai'], 2) }}</div>
                                <div class="val-pct">{{ number_format($area['persen'], 1) }}%</div>
                            </td>
                        @endforeach

                        <!-- Jumlah Pengungkit -->
                        @php
                            $isPengungkitPassed = $row['compliance']['total_pengungkit']['is_passed'];
                        @endphp
                        <td class="border-r-bold {{ !$isPengungkitPassed ? 'failed-cell' : '' }}">
                            <div class="val-num" style="font-weight: 800;">{{ number_format($row['pengungkit']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['pengungkit']['persen'], 1) }}%</div>
                        </td>

                        <!-- SPAK -->
                        @php
                            $isSpakPassed = $row['compliance']['spak']['is_passed'];
                        @endphp
                        <td class="border-r-light {{ !$isSpakPassed ? 'failed-cell' : '' }}">
                            <div class="val-num">{{ number_format($row['spak']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['spak']['persen'], 1) }}%</div>
                        </td>

                        <!-- Capaian Kinerja -->
                        @php
                            $isCapaianPassed = $row['compliance']['capaian']['is_passed'];
                        @endphp
                        <td class="border-r-light {{ !$isCapaianPassed ? 'failed-cell' : '' }}">
                            <div class="val-num">{{ number_format($row['capaian']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['capaian']['persen'], 1) }}%</div>
                        </td>

                        <!-- Birokrasi Bersih -->
                        @php
                            $isBirokrasiPassed = $row['compliance']['birokrasi_total']['is_passed'];
                        @endphp
                        <td class="border-r-light {{ !$isBirokrasiPassed ? 'failed-cell' : '' }}">
                            <div class="val-num" style="font-weight: 800;">{{ number_format($row['birokrasi']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['birokrasi']['persen'], 1) }}%</div>
                        </td>

                        <!-- SPP / Pelayanan -->
                        @php
                            $isPelayananPassed = $row['compliance']['pelayanan']['is_passed'];
                        @endphp
                        <td class="border-r-bold {{ !$isPelayananPassed ? 'failed-cell' : '' }}">
                            <div class="val-num">{{ number_format($row['pelayanan']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['pelayanan']['persen'], 1) }}%</div>
                        </td>

                        <!-- Total (100%) -->
                        @php
                            $isTotalPassed = $row['compliance']['total_zi']['is_passed'];
                        @endphp
                        <td class="border-r-bold {{ !$isTotalPassed ? 'failed-cell' : '' }}">
                            <div class="val-num" style="font-weight: 800; font-size: 9px;">{{ number_format($row['total']['nilai'], 2) }}</div>
                            <div class="val-pct">{{ number_format($row['total']['persen'], 1) }}%</div>
                        </td>

                        <!-- Simpulan -->
                        <td>
                            @if ($row['meets_wbk'])
                                <span class="badge badge-success">Memenuhi</span>
                            @else
                                <span class="badge badge-danger">Belum</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>
