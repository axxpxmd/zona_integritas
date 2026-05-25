<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Opd;
use App\Models\Komponen;
use App\Models\SubKategori;
use App\Models\Periode;
use App\Models\Indikator;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Get available periods
        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $periodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        $activePeriode = $periodeId ? Periode::find($periodeId) : null;

        $submittedOpds = collect();

        if ($activePeriode) {
            // Get distinct OPDs that have submitted their answers (status = 'final') for the selected period
            $query = Jawaban::where('periode_id', $activePeriode->id)
                ->where('status', 'final')
                ->distinct();

            if (Auth::user()->role === 'verifikator') {
                $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                    ->where('user_id', Auth::id())
                    ->pluck('opd_id');
                $query->whereIn('opd_id', $assignedOpdIds);
            }

            $opdIds = $query->pluck('opd_id');

            $submittedOpds = Opd::whereIn('id', $opdIds)->get();

            $totalRequired = \App\Models\Pertanyaan::where('status', 1)->count();

            // For each OPD, we can also get the date they submitted (max updated_at where status=final)
            foreach ($submittedOpds as $opd) {
                $lastSubmit = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->where('status', 'final')
                    ->max('updated_at');

                $opd->submitted_at = $lastSubmit;

                // Verifikasi stats per OPD
                $opd_base = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $opd->total_jawaban = $opd_base->count();
                $opd->terverifikasi = (clone $opd_base)->whereIn('status_verifikasi', ['disetujui', 'terkirim'])->count();
                $opd->terkirim = (clone $opd_base)->where('status_verifikasi', 'terkirim')->count();
                $opd->direvisi = (clone $opd_base)->where('status_verifikasi', 'direvisi')->count();
                $opd->belum_terverifikasi = (clone $opd_base)->where('status_verifikasi', 'belum_diverifikasi')->count();
                $opd->menunggu_dicek_ulang = (clone $opd_base)->where('menunggu_dicek_ulang', true)->count();
                $opd->total_pertanyaan = $totalRequired;
                $opd->persen = $opd->total_jawaban > 0
                    ? min(100, round((($opd->terverifikasi + $opd->direvisi) / $opd->total_jawaban) * 100))
                    : 0;
            }
        }

        return view('page.verifikasi.index', compact('periodes', 'activePeriode', 'submittedOpds'));
    }

    public function show(Periode $periode, Opd $opd)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $komponens = Komponen::with(['kategoris.subKategoris.indikators.pertanyaans.subPertanyaans'])
            ->orderBy('urutan')
            ->get();

        // Get all answers by this OPD to calculate progress
        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->with('files')
            ->get();

        $jawabanMap = [];
        $verifikasiStats = [
            'total_jawaban' => $jawabans->count(),
            'belum_diverifikasi' => $jawabans->where('status_verifikasi', 'belum_diverifikasi')->count(),
            'disetujui' => $jawabans->where('status_verifikasi', 'disetujui')->count(),
            'terkirim' => $jawabans->where('status_verifikasi', 'terkirim')->count(),
            'direvisi' => $jawabans->where('status_verifikasi', 'direvisi')->count(),
        ];

        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $jawabansParent = $jawabans->whereNull('sub_pertanyaan_id')->keyBy('pertanyaan_id');
        $progress = [];
        $totalSemuaPertanyaan = 0;
        $totalPertanyaanTerjawab = 0;
        $totalPertanyaanTerverifikasi = 0;

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalPertanyaan = 0;
                    $pertanyaanTerjawab = 0;
                    $pertanyaanTerverifikasi = 0;
                    $totalNilaiSubKategori = 0;

                    foreach ($subKategori->indikators as $indikator) {
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorVerifikasi($indikator, $jawabanMap);

                        $totalPertanyaan += $nilaiIndikatorData['total_pertanyaan'];
                        $totalSemuaPertanyaan += $nilaiIndikatorData['total_pertanyaan'];

                        $pertanyaanTerjawab += $nilaiIndikatorData['pertanyaan_terjawab'];
                        $totalPertanyaanTerjawab += $nilaiIndikatorData['pertanyaan_terjawab'];

                        $pertanyaanTerverifikasi += $nilaiIndikatorData['pertanyaan_terverifikasi'];
                        $totalPertanyaanTerverifikasi += $nilaiIndikatorData['pertanyaan_terverifikasi'];

                        $totalNilaiSubKategori += $nilaiIndikatorData['nilai_indikator'];
                    }

                    $persenCapaian = $subKategori->bobot > 0 ? ($totalNilaiSubKategori / $subKategori->bobot) * 100 : 0;

                    $progress[$subKategori->id] = [
                        'total' => $totalPertanyaan,
                        'terverifikasi' => $pertanyaanTerverifikasi,
                        'persen' => $totalPertanyaan > 0 ? round(($pertanyaanTerverifikasi / $totalPertanyaan) * 100) : 0,
                        'nilai' => $totalNilaiSubKategori,
                        'capaian' => $persenCapaian,
                    ];
                }
            }
        }

        $verifikasiStats['total_pertanyaan'] = $totalSemuaPertanyaan;
        $verifikasiStats['terverifikasi'] = $totalPertanyaanTerverifikasi;
        $verifikasiStats['disetujui'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'disetujui')->count();
        $verifikasiStats['terkirim'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'terkirim')->count();
        $verifikasiStats['direvisi'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'direvisi')->count();
        $verifikasiStats['belum_terverifikasi'] = max(0, $totalSemuaPertanyaan - $totalPertanyaanTerverifikasi - $verifikasiStats['direvisi']);

        $isAllAnswered = ($totalSemuaPertanyaan > 0 && $totalSemuaPertanyaan === $totalPertanyaanTerjawab);
        $isSent = $jawabans->where('status', 'final')->isNotEmpty();

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');
        $totalJawaban = (clone $opdBase)->count();
        $totalBelum = (clone $opdBase)->where('status_verifikasi', 'belum_diverifikasi')->count();
        $totalDirevisi = (clone $opdBase)->where('status_verifikasi', 'direvisi')->count();
        $totalTerkirim = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();
        $isReadySendMenpan = $totalJawaban > 0 && $totalBelum === 0 && $totalDirevisi === 0;
        $isSentToMenpan = $totalJawaban > 0 && $totalTerkirim === $totalJawaban;

        return view('page.verifikasi.show', compact('periode', 'opd', 'komponens', 'jawabanMap', 'verifikasiStats', 'progress', 'isAllAnswered', 'isSent', 'isReadySendMenpan', 'isSentToMenpan'));
    }

    public function detail(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $subKategori->load(['indikators.pertanyaans.subPertanyaans', 'kategori.komponen']);

        // Pagination indikator
        $indikators = $subKategori->indikators;
        $totalIndikator = $indikators->count();

        if ($totalIndikator == 0) {
            return redirect()->back()->with('error', 'Tidak ada indikator pada sub kategori ini.');
        }

        $currentPage = (int) max(1, min($request->get('indikator', 1), $totalIndikator));
        $currentIndikator = $indikators->get($currentPage - 1);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $nilaiIndikator = $this->hitungNilaiIndikatorVerifikasi($currentIndikator, $jawabanMap);

        // Cek apakah masih dalam masa verifikasi
        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        return view('page.verifikasi.detail', compact('periode', 'opd', 'subKategori', 'currentIndikator', 'currentPage', 'totalIndikator', 'jawabanMap', 'nilaiIndikator', 'isCanVerify', 'startVerif', 'endVerif'));
    }

    public function store(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Blokir penyimpanan jika di luar masa verifikasi
        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        if (!$isCanVerify) {
            return redirect()->back()->with('error', 'Verifikasi tidak dapat dilakukan karena di luar masa waktu verifikasi.');
        }

        $verifikasiData = $request->input('verifikasi');
        $currentPage = $request->input('current_page', 1);

        if ($verifikasiData && is_array($verifikasiData)) {
            foreach ($verifikasiData as $pertanyaanId => $data) {
                // Update semua jawaban yang terkait dengan pertanyaan ini
                $jawabans = Jawaban::where('periode_id', $periode->id)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->whereNotIn('status_verifikasi', ['direvisi', 'terkirim'])
                    ->get();

                foreach ($jawabans as $jawaban) {
                    $jawaban->status_verifikasi = $data['status_verifikasi'] ?? 'belum_diverifikasi';

                    if (isset($data['catatan_verifikator'])) {
                        $jawaban->catatan_verifikator = $data['catatan_verifikator'];
                    }

                    if (isset($data['verifikator_jawaban_angka']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['verifikator_jawaban_angka'])) {
                        $val = $data['verifikator_jawaban_angka'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->verifikator_jawaban_angka = ($val !== null && $val !== '') ? $val : null;
                    }

                    if (isset($data['verifikator_jawaban_text']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['verifikator_jawaban_text'])) {
                        $val = $data['verifikator_jawaban_text'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->verifikator_jawaban_text = ($val !== null && $val !== '') ? $val : null;
                    }

                    $verifikatorChanged = $jawaban->isDirty([
                        'status_verifikasi',
                        'verifikator_jawaban_text',
                        'verifikator_jawaban_angka',
                    ]);

                    if ($verifikatorChanged) {
                        $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
                        $jawaban->menpan_jawaban_text = null;
                        $jawaban->menpan_jawaban_angka = null;
                        $jawaban->menpan_verified_by = null;
                        $jawaban->menpan_verified_at = null;
                    }

                    if ($jawaban->status_verifikasi != 'belum_diverifikasi') {
                        $jawaban->verified_by = Auth::id();
                        $jawaban->verified_at = now();
                    } else {
                        $jawaban->verified_by = null;
                        $jawaban->verified_at = null;
                    }

                    $jawaban->save();
                }
            }
        }

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Data verifikasi berhasil disimpan.');
    }

    public function cancelPertanyaan(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'belum_diverifikasi';
            $jawaban->verified_by = null;
            $jawaban->verified_at = null;
            $jawaban->menunggu_dicek_ulang = false;
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Verifikasi pertanyaan berhasil dibatalkan.');
    }

    /**
     * Kirim permintaan revisi ke operator untuk satu pertanyaan.
     * Status menjadi 'direvisi', catatan verifikator disimpan, dan menunggu_dicek_ulang direset.
     */
    public function kirimRevisi(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string|max:2000',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi sebelum mengirim permintaan revisi.',
        ]);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'direvisi';
            $jawaban->catatan_revisi = $request->input('catatan_revisi');
            $jawaban->verified_by = Auth::id();
            $jawaban->verified_at = now();
            $jawaban->menunggu_dicek_ulang = false; // operator belum merespons
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Permintaan revisi berhasil dikirim ke operator.');
    }

    /**
     * Batalkan permintaan revisi pertanyaan.
     * Status kembali menjadi 'belum_diverifikasi' dan catatan revisi dihapus.
     */
    public function cancelRevisi(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'belum_diverifikasi';
            $jawaban->catatan_revisi = null;
            $jawaban->verified_by = null;
            $jawaban->verified_at = null;
            $jawaban->menunggu_dicek_ulang = false;
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Permintaan revisi berhasil dibatalkan.');
    }

    public function verifyAllDev(Request $request, Periode $periode, Opd $opd)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil semua jawaban OPD terkait di periode ini yang masih "belum_diverifikasi"
        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'belum_diverifikasi')
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'disetujui';

            // Set nilai verifikator agar tidak (Null) dengan menduplikat jawaban operator
            $jawaban->verifikator_jawaban_text = $jawaban->jawaban_text;
            $jawaban->verifikator_jawaban_angka = $jawaban->jawaban_angka;

            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;

            $jawaban->verified_by = Auth::id();
            $jawaban->verified_at = now();
            $jawaban->save();
        }

        return redirect()->route('verifikasi.show', ['periode' => $periode->id, 'opd' => $opd->id])
            ->with('success', '[DEV] Semua kuesioner pada OPD ini telah berhasil diverifikasi secara otomatis.');
    }

    /**
     * Kirim hasil verifikasi ke Verifikator Menpan.
     */
    public function kirimMenpan(Request $request, Periode $periode, Opd $opd)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');

        $totalJawaban = (clone $opdBase)->count();
        $totalDisetujui = (clone $opdBase)->where('status_verifikasi', 'disetujui')->count();
        $totalTerkirim = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();

        if ($totalJawaban === 0) {
            return redirect()->back()->with('error', 'Belum ada jawaban yang dapat dikirim.');
        }

        if ($totalTerkirim === $totalJawaban) {
            return redirect()->back()->with('success', 'Hasil verifikasi sudah terkirim ke Menpan.');
        }

        if (($totalDisetujui + $totalTerkirim) < $totalJawaban) {
            return redirect()->back()->with('error', 'Masih ada jawaban yang belum disetujui.');
        }

        Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'disetujui')
            ->update(['status_verifikasi' => 'terkirim']);

        return redirect()->route('verifikasi.show', ['periode' => $periode->id, 'opd' => $opd->id])
            ->with('success', 'Hasil verifikasi berhasil dikirim ke Verifikator Menpan.');
    }

    private function hitungNilaiIndikatorVerifikasi(Indikator $indikator, array $jawabanMap): array
    {
        $pertanyaans = $indikator->pertanyaans;
        $totalPertanyaan = $pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $pertanyaanTerverifikasi = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($pertanyaans as $pertanyaan) {
            $nilai = null;
            $terjawab = false;
            $isVerified = false;

            if ($pertanyaan->has_sub_pertanyaan) {
                $jawabanSub = [];
                foreach ($pertanyaan->subPertanyaans as $subPertanyaan) {
                    $key = $pertanyaan->id . '_' . $subPertanyaan->id;
                    $jawabanSubModel = $jawabanMap[$key] ?? null;
                    if ($jawabanSubModel) {
                        if (in_array($jawabanSubModel->status_verifikasi, ['disetujui', 'terkirim'], true)) {
                            $isVerified = true;
                        }
                        $value = $jawabanSubModel->verifikator_jawaban_angka;
                        if ($value === null || $value === '') {
                            $value = $jawabanSubModel->jawaban_angka;
                        }
                        if ($value !== null && $value !== '') {
                            $jawabanSub[$subPertanyaan->id] = $value;
                        }
                    }
                }

                if (count($jawabanSub) >= 2) {
                    $nilai = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSub);
                    $terjawab = true;
                }
            } else {
                $jawaban = $jawabanMap[$pertanyaan->id] ?? null;
                if ($jawaban) {
                    if (in_array($jawaban->status_verifikasi, ['disetujui', 'terkirim'], true)) {
                        $isVerified = true;
                    }
                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                        $value = $jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text;
                    } else {
                        $value = $jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka;
                    }

                    if ($value !== null && $value !== '') {
                        $nilai = $this->hitungNilai($pertanyaan, $value);
                        $terjawab = true;
                    }
                }
            }

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $terjawab,
            ];

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $pertanyaanTerjawab++;
            }

            if ($isVerified) {
                $pertanyaanTerverifikasi++;
            }
        }

        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return [
            'total_pertanyaan' => $totalPertanyaan,
            'pertanyaan_terjawab' => $pertanyaanTerjawab,
            'pertanyaan_terverifikasi' => $pertanyaanTerverifikasi,
            'rata_rata_nilai' => round($rataRataNilai, 2),
            'bobot' => $indikator->bobot,
            'nilai_indikator' => round($nilaiIndikator, 2),
            'persen_capaian' => round($persenCapaian, 2),
            'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
        ];
    }

    private function hitungNilai(Pertanyaan $pertanyaan, $jawaban): ?float
    {
        if ($jawaban === null || $jawaban === '') {
            return null;
        }

        $tipe = $pertanyaan->tipe_jawaban;

        if ($tipe === 'ya_tidak') {
            return strtolower((string) $jawaban) === 'ya' ? 1.0 : 0.0;
        }

        if ($tipe === 'pilihan_ganda') {
            $jumlahOpsi = count($pertanyaan->penjelasan_list);
            $opsi = strtoupper((string) $jawaban);
            $skorMap = $this->getSkorMap($jumlahOpsi);

            return $skorMap[$opsi] ?? null;
        }

        if ($tipe === 'angka') {
            $angka = floatval($jawaban);

            if (
                str_contains($pertanyaan->pertanyaan, 'Nilai Survey Persepsi Korupsi (Survei Eksternal)') ||
                str_contains($pertanyaan->pertanyaan, 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
            ) {
                return $angka / 4;
            }

            if ($angka > 1 && $angka <= 100) {
                return $angka / 100;
            }

            return $angka;
        }

        return null;
    }

    private function getSkorMap(int $jumlahOpsi): array
    {
        switch ($jumlahOpsi) {
            case 2:
                return [
                    'A' => 1.0,
                    'B' => 0.0,
                ];
            case 3:
                return [
                    'A' => 1.0,
                    'B' => 0.5,
                    'C' => 0.0,
                ];
            case 4:
                return [
                    'A' => 1.0,
                    'B' => 0.67,
                    'C' => 0.33,
                    'D' => 0.0,
                ];
            case 5:
                return [
                    'A' => 1.0,
                    'B' => 0.75,
                    'C' => 0.5,
                    'D' => 0.25,
                    'E' => 0.0,
                ];
            default:
                $map = [];
                $letters = range('A', chr(64 + $jumlahOpsi));
                foreach ($letters as $index => $letter) {
                    $map[$letter] = round(1 - ($index / ($jumlahOpsi - 1)), 2);
                }
                return $map;
        }
    }

    private function hitungNilaiSubPertanyaan(Pertanyaan $pertanyaan, array $jawabanSubArray): ?float
    {
        if (count($jawabanSubArray) < 2) {
            return null;
        }

        $subPertanyaans = $pertanyaan->subPertanyaans()->orderBy('urutan')->get();
        if ($subPertanyaans->count() < 2) {
            return null;
        }

        // Default: sub ke-1 = acuan (penyebut), sub ke-2 = realisasi (pembilang)
        // Konsisten dengan KuesionerController
        $idAcuan = $subPertanyaans->first()->id;
        $idRealisasi = $subPertanyaans->get(1)->id; // index 1 = sub ke-2

        // Penyesuaian untuk pertanyaan spesifik
        if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
            $idRealisasi = $subPertanyaans->get(1)->id;
        } elseif (str_contains($pertanyaan->pertanyaan, 'Persentase penyampaian LHKPN')) {
            $idRealisasi = $subPertanyaans->where('urutan', 5)->first()->id ?? $subPertanyaans->last()->id;
        }

        $nilaiAcuan = floatval($jawabanSubArray[$idAcuan] ?? 0);
        $nilaiRealisasi = floatval($jawabanSubArray[$idRealisasi] ?? 0);

        if ($nilaiAcuan > 0) {
            if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
                $capaian = ($nilaiAcuan - $nilaiRealisasi) / $nilaiAcuan;
                return max(0, min($capaian, 1.0));
            }

            $capaian = $nilaiRealisasi / $nilaiAcuan;
            return min($capaian, 1.0);
        }

        return null;
    }
}
