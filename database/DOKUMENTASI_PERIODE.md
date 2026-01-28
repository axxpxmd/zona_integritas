# Database Schema - Sistem Periode Kuesioner Zona Integritas

## 📅 Konsep Periode & Versioning

Sistem ini mendukung pengisian kuesioner per periode dengan fitur:
- ✅ Jadwal mulai dan selesai pengisian per periode
- ✅ Versioning data master (komponen, kategori, pertanyaan) per periode
- ✅ Copy/duplicate data dari periode sebelumnya untuk periode baru
- ✅ Modifikasi data master tanpa mempengaruhi periode lain

---

## 🗂️ Struktur Tabel Lengkap

### **1. tm_periode** (Tabel Master Periode)

```sql
tm_periode
├── id
├── tahun (2024, 2025, 2026)
├── nama_periode ("Zona Integritas 2024")
├── tanggal_mulai (2024-01-01)
├── tanggal_selesai (2024-12-31)
├── deskripsi
├── status (draft, aktif, selesai, ditutup)
├── is_template (0/1) - untuk template copy
├── copied_from_periode_id (FK → tm_periode)
└── timestamps
```

**Status Periode:**
- `draft` - Periode belum dibuka, master data masih bisa diedit
- `aktif` - Periode sedang berjalan, OPD bisa mengisi
- `selesai` - Sudah melewati deadline, tidak bisa isi lagi
- `ditutup` - Sudah finalisasi, tidak bisa edit sama sekali

---

### **2. Master Data dengan Versioning**

Semua tabel master ditambahkan `periode_id` untuk versioning:

#### **tm_komponen**
```sql
tm_komponen
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── kode (A, B)
├── nama (PENGUNGKIT, HASIL)
├── bobot
└── UNIQUE(periode_id, kode)
```

#### **tm_kategori**
```sql
tm_kategori
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── komponen_id (FK → tm_komponen)
├── kode (I, II)
└── nama
```

#### **tm_sub_kategori**
```sql
tm_sub_kategori
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── kategori_id (FK → tm_kategori)
├── kode (1, 2, 3, 4, 5, 6)
└── nama
```

#### **tm_indikator**
```sql
tm_indikator
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── sub_kategori_id (FK → tm_sub_kategori)
├── kode (i, ii, iii, iv, v, vi)
└── nama
```

#### **tm_pertanyaan**
```sql
tm_pertanyaan
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── indikator_id (FK → tm_indikator)
├── kode (a, b, c, d, e, f)
├── pertanyaan
├── tipe_jawaban
└── pilihan_jawaban (JSON)
```

#### **tm_sub_pertanyaan**
```sql
tm_sub_pertanyaan
├── id
├── periode_id (FK → tm_periode) ⭐ BARU
├── pertanyaan_id (FK → tm_pertanyaan)
├── kode (-, a, b, c)
├── pertanyaan
├── formula
└── tipe_input
```

---

### **3. Tabel Transaksi (Jawaban)**

#### **kuesioner_jawaban**
```sql
kuesioner_jawaban
├── id
├── periode_id (FK → tm_periode) ⭐ UPDATED
├── opd_id (FK → tm_opd)
├── pertanyaan_id (FK → tm_pertanyaan)
├── user_id (FK → users)
├── jawaban
├── nilai
├── status_verifikasi (pending, verified, rejected)
├── verifikator_id
└── UNIQUE(periode_id, opd_id, pertanyaan_id)
```

#### **kuesioner_sub_jawaban**
```sql
kuesioner_sub_jawaban
├── id
├── jawaban_id (FK → kuesioner_jawaban)
├── sub_pertanyaan_id (FK → tm_sub_pertanyaan)
├── nilai_input
└── nilai_hasil
```

---

## 🔄 Workflow Penggunaan Periode

### **Skenario 1: Membuat Periode Baru (Tahun Pertama)**

```php
// 1. Buat periode
$periode = Periode::create([
    'tahun' => 2024,
    'nama_periode' => 'Zona Integritas 2024',
    'tanggal_mulai' => '2024-01-01',
    'tanggal_selesai' => '2024-12-31',
    'status' => 'draft'
]);

// 2. Buat master data untuk periode ini
Komponen::create([
    'periode_id' => $periode->id,
    'kode' => 'A',
    'nama' => 'PENGUNGKIT',
    'bobot' => 60
]);

// 3. Lanjutkan buat kategori, indikator, pertanyaan, dst...

// 4. Aktifkan periode
$periode->update(['status' => 'aktif']);
```

---

### **Skenario 2: Copy Periode untuk Tahun Berikutnya**

```php
// 1. Buat periode baru
$periodeBaru = Periode::create([
    'tahun' => 2025,
    'nama_periode' => 'Zona Integritas 2025',
    'tanggal_mulai' => '2025-01-01',
    'tanggal_selesai' => '2025-12-31',
    'status' => 'draft',
    'copied_from_periode_id' => $periodeLama->id
]);

// 2. Copy semua master data dari periode lama
$komponenLama = Komponen::where('periode_id', $periodeLama->id)->get();
foreach ($komponenLama as $komponen) {
    $komponenBaru = $komponen->replicate();
    $komponenBaru->periode_id = $periodeBaru->id;
    $komponenBaru->save();
    
    // Map old ID to new ID untuk FK
    $mapKomponen[$komponen->id] = $komponenBaru->id;
}

// 3. Lanjutkan copy kategori, indikator, pertanyaan dengan update FK
// 4. Edit data yang berubah (jika ada)
// 5. Aktifkan periode
```

---

### **Skenario 3: Mengisi Kuesioner**

```php
// 1. Cek periode aktif
$periodeAktif = Periode::where('status', 'aktif')
    ->where('tanggal_mulai', '<=', now())
    ->where('tanggal_selesai', '>=', now())
    ->first();

// 2. Load pertanyaan untuk periode ini
$pertanyaan = Pertanyaan::where('periode_id', $periodeAktif->id)->get();

// 3. Simpan jawaban
Jawaban::create([
    'periode_id' => $periodeAktif->id,
    'opd_id' => auth()->user()->opd_id,
    'pertanyaan_id' => $pertanyaan->id,
    'user_id' => auth()->id(),
    'jawaban' => 'A',
    'nilai' => 1.0
]);
```

---

### **Skenario 4: Edit Pertanyaan di Periode Baru Tanpa Pengaruh Periode Lama**

```php
// Data periode 2024 tetap aman
$periode2024 = Periode::find(1);
$pertanyaan2024 = Pertanyaan::where('periode_id', $periode2024->id)->first();
// "Unit kerja telah membentuk tim..."

// Edit di periode 2025 tanpa pengaruh periode 2024
$periode2025 = Periode::find(2);
$pertanyaan2025 = Pertanyaan::where('periode_id', $periode2025->id)
    ->where('kode', 'a')
    ->first();
    
$pertanyaan2025->update([
    'pertanyaan' => 'Unit kerja telah membentuk tim reformasi birokrasi...' // Teks berubah
]);

// Periode 2024 tidak terpengaruh, data tetap historis
```

---

## 📊 Hierarki Lengkap dengan Periode

```
tm_periode (2024, 2025, 2026)
  │
  ├─► tm_komponen (periode_id)
  │     └─► tm_kategori (periode_id)
  │           └─► tm_sub_kategori (periode_id)
  │                 └─► tm_indikator (periode_id)
  │                       └─► tm_pertanyaan (periode_id)
  │                             └─► tm_sub_pertanyaan (periode_id)
  │
  └─► kuesioner_jawaban (periode_id)
        └─► kuesioner_sub_jawaban
```

---

## ✅ Keuntungan Sistem Periode

1. **Historis Data** - Data lama tetap tersimpan dan tidak berubah
2. **Fleksibilitas** - Bisa edit pertanyaan untuk tahun baru tanpa merusak data lama
3. **Jadwal Terkontrol** - Tanggal mulai/selesai otomatis validasi
4. **Easy Copy** - Duplikasi data dari periode sebelumnya dengan 1 klik
5. **Multi-Version** - Bisa jalankan beberapa periode paralel (misal: WBK & WBBM berbeda)
6. **Audit Trail** - Tahu periode mana yang di-copy dari mana

---

## 🚀 Migration Files

File migration yang dibuat:
1. `2026_01_27_000001_create_komponen_table.php`
2. `2026_01_27_000002_create_kategori_table.php`
3. `2026_01_27_000003_create_sub_kategori_table.php`
4. `2026_01_27_000004_create_indikator_table.php`
5. `2026_01_27_000005_create_pertanyaan_table.php`
6. `2026_01_27_000006_create_kuesioner_jawaban_table.php` ⭐ UPDATED
7. `2026_01_27_000007_create_sub_pertanyaan_table.php`
8. `2026_01_27_000008_create_kuesioner_sub_jawaban_table.php`
9. `2026_01_27_000009_create_periode_table.php` ⭐ NEW
10. `2026_01_27_000010_add_periode_to_master_tables.php` ⭐ NEW

**Jalankan:**
```bash
php artisan migrate
```
