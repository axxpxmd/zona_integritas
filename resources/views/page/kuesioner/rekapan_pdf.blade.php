<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Hasil LKE ZI - {{ $periode->nama_periode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0164CA;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            color: #0164CA;
            font-size: 16px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
        }
        .info-table strong {
            display: inline-block;
            width: 100px;
        }

        .role-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .role-title {
            background-color: #f4f4f4;
            color: #333;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: bold;
            border-left: 4px solid #0164CA;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .data-table th {
            background-color: #0164CA;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .bg-pengungkit { background-color: #eef6ff !important; font-weight: bold;}
        .bg-hasil { background-color: #eefbee !important; font-weight: bold;}

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #047857; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-primary { background: #dbeafe; color: #1d4ed8; }

        .grand-total-row {
            background-color: #0164CA !important;
            color: #fff !important;
            font-weight: bold;
        }
        .grand-total-row td {
            border-color: #0150A8;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAPAN HASIL LEMBAR KERJA EVALUASI (LKE)</h1>
        <h2>PEMBANGUNAN ZONA INTEGRITAS MENUJU WBK/WBBM</h2>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Unit Kerja</strong></td>
            <td>: {{ $opd->n_opd }}</td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td>: {{ $periode->nama_periode }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d-m-Y H:i') }}</td>
        </tr>
    </table>

    @php
        $roleNames = [
            'operator' => 'DARI OPERATOR (SELF ASSESSMENT)',
            'verifikator' => 'DARI VERIFIKATOR (TPI)',
            'menpan' => 'DARI MENPAN (TPN)'
        ];
    @endphp

    @foreach(['operator', 'verifikator', 'menpan'] as $role)
        @php
            $rekapPengungkit = $rekapData[$role]['rekapPengungkit'];
            $rekapHasil = $rekapData[$role]['rekapHasil'];

            $totalPengungkitBobot = 0;
            $totalPengungkitNilai = 0;
            foreach($rekapPengungkit as $area) {
                $totalPengungkitBobot += ($area['pemenuhan_bobot'] + $area['reform_bobot']);
                $totalPengungkitNilai += ($area['pemenuhan_nilai'] + $area['reform_nilai']);
            }

            $totalHasilBobot = 0;
            $totalHasilNilai = 0;
            foreach($rekapHasil as $hasil) {
                $totalHasilBobot += $hasil['bobot'];
                $totalHasilNilai += $hasil['nilai'];
            }

            $grandTotalBobot = $totalPengungkitBobot + $totalHasilBobot;
            $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;
            $grandTotalPersen = $grandTotalBobot > 0 ? ($grandTotalNilai / $grandTotalBobot) * 100 : 0;
            $pengungkitPersen = $totalPengungkitBobot > 0 ? ($totalPengungkitNilai / $totalPengungkitBobot) * 100 : 0;
            $hasilPersen = $totalHasilBobot > 0 ? ($totalHasilNilai / $totalHasilBobot) * 100 : 0;
        @endphp

        <div class="role-section">
            <div class="role-title">NILAI EVALUASI {{ $roleNames[$role] }}</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:40%;">Area Perubahan</th>
                        <th style="width:12%;">Bobot</th>
                        <th style="width:12%;">Pemenuhan</th>
                        <th style="width:12%;">Reform</th>
                        <th style="width:12%;">Nilai</th>
                        <th style="width:12%;">%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-pengungkit">
                        <td colspan="6">A. PENGUNGKIT</td>
                    </tr>
                    @foreach($rekapPengungkit as $area)
                        @php
                            $bobotArea = $area['pemenuhan_bobot'] + $area['reform_bobot'];
                            $nilaiArea = $area['pemenuhan_nilai'] + $area['reform_nilai'];
                            $persenArea = $bobotArea > 0 ? ($nilaiArea / $bobotArea) * 100 : 0;

                            $badgeClass = 'badge-danger';
                            if ($persenArea >= 90) $badgeClass = 'badge-success';
                            elseif ($persenArea >= 75) $badgeClass = 'badge-primary';
                            elseif ($persenArea >= 50) $badgeClass = 'badge-warning';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}. {{ $area['nama'] }}</td>
                            <td class="text-center">{{ number_format($bobotArea, 2) }}</td>
                            <td class="text-center">{{ number_format($area['pemenuhan_nilai'], 2) }}</td>
                            <td class="text-center">{{ number_format($area['reform_nilai'], 2) }}</td>
                            <td class="text-center font-bold">{{ number_format($nilaiArea, 2) }}</td>
                            <td class="text-center"><span class="badge {{ $badgeClass }}">{{ number_format($persenArea, 2) }}%</span></td>
                        </tr>
                    @endforeach
                    <tr style="background:#f9f9f9; font-weight:bold;">
                        <td class="text-right">Total Pengungkit (A)</td>
                        <td class="text-center">{{ number_format($totalPengungkitBobot, 2) }}</td>
                        <td colspan="2" class="text-center">-</td>
                        <td class="text-center">{{ number_format($totalPengungkitNilai, 2) }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = 'badge-danger';
                                if ($pengungkitPersen >= 90) $badgeClass = 'badge-success';
                                elseif ($pengungkitPersen >= 75) $badgeClass = 'badge-primary';
                                elseif ($pengungkitPersen >= 50) $badgeClass = 'badge-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ number_format($pengungkitPersen, 2) }}%</span>
                        </td>
                    </tr>

                    <tr class="bg-hasil">
                        <td colspan="6">B. HASIL</td>
                    </tr>
                    @foreach($rekapHasil as $hasil)
                        @php
                            $persenHasil = $hasil['bobot'] > 0 ? ($hasil['nilai'] / $hasil['bobot']) * 100 : 0;
                            $badgeClass = 'badge-danger';
                            if ($persenHasil >= 90) $badgeClass = 'badge-success';
                            elseif ($persenHasil >= 75) $badgeClass = 'badge-primary';
                            elseif ($persenHasil >= 50) $badgeClass = 'badge-warning';
                        @endphp
                        <tr style="background:#f9f9f9;">
                            <td class="font-bold">{{ $hasil['kode'] ?? $loop->iteration }}. {{ $hasil['nama'] }}</td>
                            <td class="text-center font-bold">{{ number_format($hasil['bobot'], 2) }}</td>
                            <td colspan="2" class="text-center">-</td>
                            <td class="text-center font-bold">{{ number_format($hasil['nilai'], 2) }}</td>
                            <td class="text-center"><span class="badge {{ $badgeClass }}">{{ number_format($persenHasil, 2) }}%</span></td>
                        </tr>
                        @foreach($hasil['subs'] as $sub)
                            @php
                                $persenSub = $sub['bobot'] > 0 ? ($sub['nilai'] / $sub['bobot']) * 100 : 0;
                                $subClass = 'badge-danger';
                                if ($persenSub >= 90) $subClass = 'badge-success';
                                elseif ($persenSub >= 75) $subClass = 'badge-primary';
                                elseif ($persenSub >= 50) $subClass = 'badge-warning';
                            @endphp
                            <tr>
                                <td style="padding-left: 20px;">{{ $sub['kode'] }}. {{ $sub['nama'] }}</td>
                                <td class="text-center">{{ number_format($sub['bobot'], 2) }}</td>
                                <td colspan="2" class="text-center">-</td>
                                <td class="text-center">{{ number_format($sub['nilai'], 2) }}</td>
                                <td class="text-center"><span class="badge {{ $subClass }}">{{ number_format($persenSub, 2) }}%</span></td>
                            </tr>
                        @endforeach
                    @endforeach

                    <tr style="background:#f9f9f9; font-weight:bold;">
                        <td class="text-right">Total Hasil (B)</td>
                        <td class="text-center">{{ number_format($totalHasilBobot, 2) }}</td>
                        <td colspan="2" class="text-center">-</td>
                        <td class="text-center">{{ number_format($totalHasilNilai, 2) }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = 'badge-danger';
                                if ($hasilPersen >= 90) $badgeClass = 'badge-success';
                                elseif ($hasilPersen >= 75) $badgeClass = 'badge-primary';
                                elseif ($hasilPersen >= 50) $badgeClass = 'badge-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ number_format($hasilPersen, 2) }}%</span>
                        </td>
                    </tr>

                    <tr class="grand-total-row">
                        <td class="text-right">NILAI EVALUASI ZI (A+B)</td>
                        <td class="text-center">{{ number_format($grandTotalBobot, 2) }}</td>
                        <td colspan="2" style="background:#0150A8;"></td>
                        <td class="text-center" style="font-size: 14px;">{{ number_format($grandTotalNilai, 2) }}</td>
                        <td class="text-center" style="font-size: 14px;">{{ number_format($grandTotalPersen, 2) }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if(!$loop->last)
            <!-- Spacer between roles -->
            <br>
        @endif
    @endforeach

</body>
</html>
