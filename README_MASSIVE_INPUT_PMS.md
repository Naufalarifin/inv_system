# Fitur Massive Input On PMS

## Deskripsi
Fitur ini memungkinkan pengguna untuk melakukan input massive data On PMS ke dalam inventory report dengan format yang fleksibel dan sistem perhitungan stock otomatis.

## Lokasi Fitur
- **File JavaScript**: `application/views/report/javascript_report.php`
- **File Controller**: `application/controllers/inventory.php`
- **Endpoint**: `/cdummy/inventory/save_massive_on_pms`

## Cara Penggunaan

### 1. Akses Fitur
- Buka halaman Inventory Report (`/cdummy/inventory/inv_report`)
- Klik tombol **"+ Input on pms"** di toolbar

### 2. Format Input Data
Data diinput dalam format berikut:
```
KODE_ALAT[TAB]UKURAN[TAB]WARNA[TAB]STATUS[TAB]STOCK(opsional)
```

**Contoh:**
```
ABC123	M	Merah	DN	2
XYZ789	L	Biru	LN
DEF456	XL	Hitam	DN	3
```

### 3. Aturan Input
- **Kode Alat**: Kode unik perangkat (wajib)
- **Ukuran**: Ukuran perangkat (wajib) - akan dikonversi ke uppercase
- **Warna**: Warna perangkat (wajib)
- **Status**: DN (Done) atau LN (Line) (wajib)
- **Stock**: Jumlah stock (opsional, default: 1)

### 4. Sistem Perhitungan Stock
- Jika input **tanpa stock** atau hanya 4 kolom: stock = 1
- Jika ada **data yang sama persis** (kode, ukuran, warna, status): stock dihitung otomatis
  - 2 data sama = stock = 2
  - 3 data sama = stock = 3
  - dst.
- Jika ada **stock manual**: menggunakan nilai yang diinput

### 5. Preview Data
- Sistem akan menampilkan preview data yang akan disimpan
- Menampilkan data unik setelah perhitungan stock otomatis
- Menampilkan total data yang akan diproses

## Struktur Kode

### Frontend (JavaScript)

#### Fungsi Utama:
1. **`showInputPmsModal()`**: Menampilkan modal input
2. **`loadWeekPeriodsPms()`**: Memuat data periode minggu
3. **`loadDevicesPms()`**: Memuat data device berdasarkan tech/type
4. **`updatePmsPreview()`**: Update preview data real-time
5. **`saveMassiveOnPms()`**: Kirim data ke server

#### Modal Structure:
```html
<div id="modal_input_pms" class="modal-container">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Input On PMS - Massive Input</h3>
      <button onclick="closeModal_report('modal_input_pms')">&times;</button>
    </div>
    <div class="modal-body">
      <!-- Form fields -->
    </div>
    <div class="modal-footer">
      <!-- Action buttons -->
    </div>
  </div>
</div>
```

### Backend (PHP)

#### Controller Method:
```php
public function save_massive_on_pms()
```

#### Proses:
1. **Validasi Input**: JSON, week ID, device ID, data
2. **Validasi Week**: Cek keberadaan periode minggu
3. **Validasi Device**: Cek keberadaan device
4. **Proses Data**: 
   - Parse setiap baris data
   - Validasi format dan nilai
   - Cek/update record inv_report
5. **Response**: Status success/error dengan detail

#### Database Operations:
- **Update Existing**: Jika record inv_report sudah ada
- **Create New**: Jika record belum ada, generate dulu dengan `upsertInvReport()`

## Integrasi dengan Sistem Existing

### 1. Inventory Report Generation
- Menggunakan method `upsertInvReport()` yang sudah ada
- Mengikuti logika generate data yang sama dengan fitur "Generate Data"
- Hanya mengupdate kolom `on_pms` sesuai input

### 2. Data Sources
- **Week Periods**: Dari tabel `inv_week`
- **Devices**: Dari tabel `inv_dvc` berdasarkan tech/type
- **Colors**: Dari method `get_device_colors()`
- **Report Data**: Dari tabel `inv_report`

### 3. Validation Rules
- Status harus DN atau LN
- Stock minimal 1
- Ukuran dikonversi ke uppercase
- Week dan Device harus valid

## Error Handling

### Frontend:
- Validasi format input real-time
- Preview data sebelum submit
- Konfirmasi sebelum save
- Toast notifications untuk feedback

### Backend:
- Validasi JSON input
- Validasi data integrity
- Error logging
- Detailed error messages

## CSS Styling

### Modal Styling:
```css
.modal-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
```

### Textarea Styling:
```css
.massive-textarea { 
    min-height: 120px; 
    resize: vertical; 
    font: 14px/1 monospace; 
    font-family: 'Courier New', monospace;
    line-height: 1.4;
}
```

## Testing

### Test Cases:
1. **Valid Input**: Data dengan format benar
2. **Duplicate Data**: Data yang sama untuk test perhitungan stock
3. **Invalid Status**: Status selain DN/LN
4. **Empty Fields**: Field yang kosong
5. **Large Data**: Input dengan banyak baris data

### Expected Results:
- Stock otomatis terhitung untuk data duplikat
- Error message untuk data invalid
- Success message untuk data valid
- Data tersimpan di database dengan benar

## Dependencies

### Frontend:
- JavaScript ES6+
- Fetch API
- DOM manipulation

### Backend:
- CodeIgniter 3
- MySQL database
- JSON handling

### Database Tables:
- `inv_week`: Data periode minggu
- `inv_dvc`: Data device
- `inv_report`: Data inventory report

## Security Considerations

1. **Input Validation**: Validasi semua input di frontend dan backend
2. **SQL Injection**: Menggunakan prepared statements
3. **XSS Prevention**: Escape output HTML
4. **CSRF Protection**: Menggunakan token jika diperlukan

## Performance Considerations

1. **Batch Processing**: Proses data dalam batch untuk performa
2. **Database Indexing**: Pastikan index pada kolom yang sering diquery
3. **Memory Management**: Handle large input data dengan efisien
4. **Error Recovery**: Rollback jika terjadi error

## Future Enhancements

1. **File Upload**: Support upload file Excel/CSV
2. **Template Download**: Download template input
3. **Bulk Validation**: Validasi semua data sebelum save
4. **Progress Indicator**: Progress bar untuk data besar
5. **Undo/Redo**: Fitur undo untuk perubahan
