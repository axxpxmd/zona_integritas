<?php

namespace App\Http\Controllers;

use App\Models\{Periode, Komponen, Kategori, SubKategori, Indikator, Pertanyaan, Jawaban};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('page.kuesioner.form', compact('periode', 'opd', 'subKategori', 'jawabans', 'currentIndikator', 'currentPage', 'totalIndikator'));
    }

    /**
     * Simpan jawaban kuesioner
     */
    public function submit(Request $request)
    {
        dd($request->all());
        // TODO: Implementasi simpan jawaban
    }
}
