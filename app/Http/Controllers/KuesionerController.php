<?php

namespace App\Http\Controllers;

use App\Models\{Periode, Komponen, Kategori, SubKategori, Indikator, Pertanyaan, Jawaban};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KuesionerController extends Controller
{
    /**
     * Halaman pilih periode
     */
    public function index()
    {
        // Ambil periode yang aktif atau berlangsung
        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('page.kuesioner.index', compact('periodes'));
    }

    /**
     * Halaman pilih sub kategori berdasarkan periode
     */
    public function show($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Ambil struktur hierarki: Komponen → Kategori → SubKategori dengan progress
        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators' => function ($q) {
                    $q->where('status', 1);
                },
                'kategoris.subKategoris.indikators.pertanyaans' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->orderBy('urutan')
            ->get();

        // Hitung progress untuk setiap sub kategori
        $progress = [];
        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalPertanyaan = 0;
                    $pertanyaanTerjawab = 0;

                    foreach ($subKategori->indikators as $indikator) {
                        foreach ($indikator->pertanyaans as $pertanyaan) {
                            $totalPertanyaan++;

                            // Cek apakah pertanyaan ini sudah dijawab
                            $jawaban = Jawaban::where('periode_id', $periode_id)
                                ->where('opd_id', $opd->id)
                                ->where('pertanyaan_id', $pertanyaan->id)
                                ->whereNull('sub_pertanyaan_id')
                                ->exists();

                            if ($jawaban) {
                                $pertanyaanTerjawab++;
                            }
                        }
                    }

                    $progress[$subKategori->id] = [
                        'total' => $totalPertanyaan,
                        'terjawab' => $pertanyaanTerjawab,
                        'persen' => $totalPertanyaan > 0 ? round(($pertanyaanTerjawab / $totalPertanyaan) * 100) : 0
                    ];
                }
            }
        }

        return view('page.kuesioner.pilih-sub-kategori', compact('periode', 'opd', 'komponens', 'progress'));
    }

    /**
     * Halaman form isi kuesioner per sub kategori
     */
    public function fill(Request $request, $periode_id, $sub_kategori_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $subKategori = SubKategori::with([
            'kategori.komponen',
            'indikators' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            },
            'indikators.pertanyaans' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            },
            'indikators.pertanyaans.subPertanyaans' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            }
        ])->findOrFail($sub_kategori_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Pagination indikator
        $indikators = $subKategori->indikators;
        $totalIndikator = $indikators->count();
        $currentPage = (int) max(1, min($request->get('indikator', 1), $totalIndikator));
        $currentIndikator = $indikators->get($currentPage - 1);

        if (!$currentIndikator) {
            return redirect()->route('kuesioner.fill', [$periode_id, $sub_kategori_id, 'indikator' => 1]);
        }

        // Ambil jawaban yang sudah diisi oleh OPD ini untuk periode ini
        $jawabans = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->get()
            ->keyBy(function ($item) {
                // Key: pertanyaan_id atau pertanyaan_id-sub_pertanyaan_id
                return $item->sub_pertanyaan_id
                    ? $item->pertanyaan_id . '-' . $item->sub_pertanyaan_id
                    : $item->pertanyaan_id;
            });

        // Hitung nilai per indikator berdasarkan formula Excel:
        // Nilai Indikator = AVERAGE(nilai semua pertanyaan) × bobot
        $nilaiIndikator = $this->hitungNilaiIndikator($currentIndikator, $jawabans);

        return view('page.kuesioner.form', compact('periode', 'opd', 'subKategori', 'jawabans', 'currentIndikator', 'currentPage', 'totalIndikator', 'nilaiIndikator'));
    }

    /**
     * Simpan jawaban kuesioner
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->back()->with('error', 'User belum terhubung dengan OPD');
        }

        $request->validate([
            'periode_id' => 'required|exists:tm_periode,id',
            'sub_kategori_id' => 'required|exists:tm_sub_kategori,id',
            'indikator_id' => 'required|exists:tm_indikator,id',
            'pertanyaan_id' => 'required|array',
            'jawaban' => 'nullable|array',
            'keterangan' => 'nullable|array',
            'file' => 'nullable|array',
        ]);

        $periodeId = $request->periode_id;
        $subKategoriId = $request->sub_kategori_id;
        $currentPage = $request->current_page;
        $totalIndikator = $request->total_indikator;
        $pertanyaanIds = $request->pertanyaan_id;
        $jawabanData = $request->jawaban ?? [];
        $keteranganData = $request->keterangan ?? [];
        $fileData = $request->file('file') ?? [];

        foreach ($pertanyaanIds as $pertanyaanId) {
            // Skip jika tidak ada jawaban dan keterangan untuk pertanyaan ini
            $jawaban = $jawabanData[$pertanyaanId] ?? null;
            $keterangan = $keteranganData[$pertanyaanId] ?? null;
            $file = $fileData[$pertanyaanId] ?? null;

            // Skip jika tidak ada data sama sekali
            if (empty($jawaban) && empty($keterangan) && empty($file)) {
                continue;
            }

            $pertanyaan = Pertanyaan::find($pertanyaanId);

            if (!$pertanyaan) {
                continue;
            }

            // Hitung nilai berdasarkan tipe jawaban dan formula Excel
            $nilai = $jawaban ? $this->hitungNilai($pertanyaan, $jawaban) : null;

            // Tentukan field yang akan disimpan
            $jawabanText = null;
            $jawabanAngka = null;

            if ($jawaban) {
                if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                    $jawabanText = $jawaban;
                } else {
                    $jawabanAngka = is_numeric($jawaban) ? $jawaban : null;
                }
            }

            // Handle file upload
            $filePath = null;
            if ($file && $file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());
                $fileName = time() . '_' . $pertanyaanId . '.' . $ext;
                $storagePath = 'kuesioner/' . $periodeId . '/' . $opd->id . '/';

                $file->storeAs($storagePath, $fileName, 'sftp');
                $filePath = $storagePath . $fileName;
            }

            // Simpan atau update jawaban
            $dataToUpdate = [
                'jawaban_text' => $jawabanText,
                'jawaban_angka' => $jawabanAngka,
                'nilai' => $nilai,
                'keterangan' => $keterangan,
                'status' => 'draft',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            if ($filePath) {
                $dataToUpdate['file_path'] = $filePath;
            }

            Jawaban::updateOrCreate(
                [
                    'periode_id' => $periodeId,
                    'opd_id' => $opd->id,
                    'pertanyaan_id' => $pertanyaanId,
                    'sub_pertanyaan_id' => null,
                ],
                $dataToUpdate
            );
        }

        // Tetap di halaman form setelah submit
        return redirect()->back()->with('success', 'Jawaban berhasil disimpan');
    }

    /**
     * Hitung nilai berdasarkan tipe jawaban
     * Formula berdasarkan Excel form kuesioner:
     * - Ya/Tidak: Ya = 1, Tidak = 0
     * - A/B/C: A = 1, B = 0.5, C = 0
     * - A/B/C/D: A = 1, B = 0.67, C = 0.33, D = 0
     * - A/B/C/D/E: A = 1, B = 0.75, C = 0.5, D = 0.25, E = 0
     * - Angka/%: Nilai langsung (persentase atau angka)
     */
    private function hitungNilai(Pertanyaan $pertanyaan, $jawaban): ?float
    {
        if (empty($jawaban)) {
            return null;
        }

        $tipe = $pertanyaan->tipe_jawaban;

        // Untuk tipe Ya/Tidak
        if ($tipe === 'ya_tidak') {
            return strtolower($jawaban) === 'ya' ? 1.0 : 0.0;
        }

        // Untuk tipe Pilihan Ganda (A/B/C, A/B/C/D, A/B/C/D/E)
        if ($tipe === 'pilihan_ganda') {
            $jumlahOpsi = count($pertanyaan->penjelasan_list);
            $opsi = strtoupper($jawaban);

            // Scoring berdasarkan jumlah opsi
            $skorMap = $this->getSkorMap($jumlahOpsi);

            return $skorMap[$opsi] ?? null;
        }

        // Untuk tipe Angka (persentase, jumlah, dll)
        if ($tipe === 'angka') {
            // Jika dalam persen (0-100), konversi ke desimal (0-1)
            $angka = floatval($jawaban);
            if ($angka > 1 && $angka <= 100) {
                return $angka / 100;
            }
            return $angka;
        }

        return null;
    }

    /**
     * Get skor mapping berdasarkan jumlah opsi
     * Sesuai dengan formula Excel
     */
    private function getSkorMap(int $jumlahOpsi): array
    {
        switch ($jumlahOpsi) {
            case 2: // Ya/Tidak atau A/B
                return [
                    'A' => 1.0,
                    'B' => 0.0,
                ];
            case 3: // A/B/C
                return [
                    'A' => 1.0,
                    'B' => 0.5,
                    'C' => 0.0,
                ];
            case 4: // A/B/C/D
                return [
                    'A' => 1.0,
                    'B' => 0.67,
                    'C' => 0.33,
                    'D' => 0.0,
                ];
            case 5: // A/B/C/D/E
                return [
                    'A' => 1.0,
                    'B' => 0.75,
                    'C' => 0.5,
                    'D' => 0.25,
                    'E' => 0.0,
                ];
            default:
                // Fallback: distribusi linear
                $map = [];
                $letters = range('A', chr(64 + $jumlahOpsi));
                foreach ($letters as $index => $letter) {
                    $map[$letter] = round(1 - ($index / ($jumlahOpsi - 1)), 2);
                }
                return $map;
        }
    }

    /**
     * Hitung nilai indikator berdasarkan formula Excel:
     * Nilai Indikator = AVERAGE(nilai semua pertanyaan) × bobot
     */
    private function hitungNilaiIndikator(Indikator $indikator, $jawabans): array
    {
        $pertanyaans = $indikator->pertanyaans;
        $totalPertanyaan = $pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($pertanyaans as $pertanyaan) {
            $jawaban = $jawabans[$pertanyaan->id] ?? null;
            $nilai = $jawaban ? $jawaban->nilai : null;

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $jawaban !== null && $jawaban->jawaban_text !== null || $jawaban !== null && $jawaban->jawaban_angka !== null,
            ];

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $pertanyaanTerjawab++;
            }
        }

        // Hitung rata-rata nilai pertanyaan
        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;

        // Nilai indikator = rata-rata × bobot
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;

        // Persentase capaian = nilai indikator / bobot × 100
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return [
            'total_pertanyaan' => $totalPertanyaan,
            'pertanyaan_terjawab' => $pertanyaanTerjawab,
            'rata_rata_nilai' => round($rataRataNilai, 2),
            'bobot' => $indikator->bobot,
            'nilai_indikator' => round($nilaiIndikator, 2),
            'persen_capaian' => round($persenCapaian, 2),
            'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
        ];
    }

    /**
     * Hitung nilai preview (AJAX) - untuk update real-time tanpa save
     */
    public function hitungNilaiPreview(Request $request)
    {
        $request->validate([
            'indikator_id' => 'required|exists:tm_indikator,id',
            'jawaban' => 'required|array',
        ]);

        $indikator = Indikator::with(['pertanyaans' => function ($q) {
            $q->where('status', 1)->orderBy('urutan');
        }])->findOrFail($request->indikator_id);

        $jawabanInput = $request->jawaban;
        $totalPertanyaan = $indikator->pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($indikator->pertanyaans as $pertanyaan) {
            $jawaban = $jawabanInput[$pertanyaan->id] ?? null;
            $nilai = null;

            if (!empty($jawaban)) {
                $nilai = $this->hitungNilai($pertanyaan, $jawaban);
                if ($nilai !== null) {
                    $totalNilai += $nilai;
                    $pertanyaanTerjawab++;
                }
            }

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => !empty($jawaban),
            ];
        }

        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_pertanyaan' => $totalPertanyaan,
                'pertanyaan_terjawab' => $pertanyaanTerjawab,
                'rata_rata_nilai' => round($rataRataNilai, 2),
                'bobot' => $indikator->bobot,
                'nilai_indikator' => round($nilaiIndikator, 2),
                'persen_capaian' => round($persenCapaian, 2),
                'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
            ],
        ]);
    }

    /**
     * Tampilkan file dokumen kuesioner yang sudah diunggah
     */
    public function viewFile($id)
    {
        $jawaban = Jawaban::findOrFail($id);

        if (!$jawaban->file_path || !Storage::disk('sftp')->exists($jawaban->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $user = Auth::user();
        // Hanya verifikator, admin, atau OPD yang memiliki jawaban ini yang boleh mengakses
        if ($user->role === 'operator' && $jawaban->opd_id !== $user->opd_id) {
            abort(403, 'Akses ditolak.');
        }

        $fileContent = Storage::disk('sftp')->get($jawaban->file_path);
        $mimeType = Storage::disk('sftp')->mimeType($jawaban->file_path);

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($jawaban->file_path) . '"'
        ]);
    }
}
