# Fitur Perhitungan Order dan Over pada Input On PMS

## Deskripsi
Fitur ini menambahkan perhitungan otomatis untuk kolom `order` dan `over` pada tabel `inv_report` saat melakukan input On PMS. Perhitungan dilakukan berdasarkan formula yang telah ditentukan.

## Lokasi Implementasi
- **Controller**: `application/controllers/inventory.php` - method `save_massive_on_pms()`
- **Model**: `application/models/report_model.php` - helper methods `calculateOrder()` dan `calculateOver()`

## Formula Perhitungan

### 1. Order Calculation
```
order = max(0, needs - on_pms - stock)
```
- **Keterangan**: Nilai minimum adalah 0
- **Logika**: Jika hasil perhitungan di bawah 0, maka order = 0
- **Contoh**:
  - needs = 10, on_pms = 3, stock = 2 → order = max(0, 10-3-2) = max(0, 5) = 5
  - needs = 5, on_pms = 8, stock = 2 → order = max(0, 5-8-2) = max(0, -5) = 0

### 2. Over Calculation
```
over = (needs - on_pms - stock < 0) ? abs(needs - on_pms - stock) : 0
```
- **Keterangan**: 
  - Jika hasil perhitungan ≥ 0, maka over = 0
  - Jika hasil perhitungan < 0, maka over = nilai absolut (positif)
- **Contoh**:
  - needs = 10, on_pms = 3, stock = 2 → over = 0 (karena 10-3-2 = 5 ≥ 0)
  - needs = 5, on_pms = 8, stock = 2 → over = abs(5-8-2) = abs(-5) = 5

## Implementasi Kode

### A. Helper Methods di Model

```php
/**
 * Calculate order value: needs - on_pms - stock (minimum 0)
 */
public function calculateOrder($needs, $on_pms, $stock) {
    return max(0, $needs - $on_pms - $stock);
}

/**
 * Calculate over value: if (needs - on_pms - stock) < 0, take positive value, else 0
 */
public function calculateOver($needs, $on_pms, $stock) {
    $calculation = $needs - $on_pms - $stock;
    return ($calculation < 0) ? abs($calculation) : 0;
}
```

### B. Penggunaan di Controller

```php
// Calculate order and over using helper methods
$order = $this->report_model->calculateOrder($current_needs, $stock, $current_stock);
$over = $this->report_model->calculateOver($current_needs, $stock, $current_stock);

$update_data = array(
    'on_pms' => $stock,
    'order' => $order,
    'over' => $over
);
```

## Alur Kerja

### 1. Update Existing Record
1. Ambil data existing dari `inv_report`
2. Hitung `current_stock` dan `current_needs`
3. Hitung `order` dan `over` menggunakan helper methods
4. Update record dengan nilai baru

### 2. Create New Record
1. Generate base data menggunakan `upsertInvReport()`
2. Ambil record yang baru dibuat
3. Hitung `order` dan `over` berdasarkan data yang ada
4. Update record dengan nilai `on_pms`, `order`, dan `over`

## Data yang Di-Generate

Setelah save input On PMS, sistem akan mengupdate:

| Kolom | Sumber | Keterangan |
|-------|--------|------------|
| `on_pms` | Input user | Nilai stock yang diinput |
| `order` | Calculated | max(0, needs - on_pms - stock) |
| `over` | Calculated | abs(needs - on_pms - stock) jika < 0, else 0 |

## Contoh Skenario

### Skenario 1: Stock Cukup
- **Input**: on_pms = 5
- **Data Existing**: needs = 10, stock = 2
- **Hasil**:
  - order = max(0, 10-5-2) = max(0, 3) = 3
  - over = 0 (karena 10-5-2 = 3 ≥ 0)

### Skenario 2: Stock Berlebih
- **Input**: on_pms = 15
- **Data Existing**: needs = 10, stock = 2
- **Hasil**:
  - order = max(0, 10-15-2) = max(0, -7) = 0
  - over = abs(10-15-2) = abs(-7) = 7

### Skenario 3: Stock Pas
- **Input**: on_pms = 8
- **Data Existing**: needs = 10, stock = 2
- **Hasil**:
  - order = max(0, 10-8-2) = max(0, 0) = 0
  - over = 0 (karena 10-8-2 = 0 ≥ 0)

## Integrasi dengan Fitur Existing

### 1. Massive Input On PMS
- Perhitungan otomatis untuk setiap item yang diinput
- Update real-time pada kolom `order` dan `over`

### 2. Inventory Report Generation
- Data yang di-generate sudah include nilai `order` dan `over`
- Konsisten dengan perhitungan manual

### 3. Data Consistency
- Nilai `order` dan `over` selalu sinkron dengan `needs`, `on_pms`, dan `stock`
- Update otomatis saat ada perubahan pada input On PMS

## Testing

### Test Cases yang Direkomendasikan
1. **Normal Case**: needs > (on_pms + stock)
2. **Edge Case**: needs = (on_pms + stock)
3. **Over Case**: needs < (on_pms + stock)
4. **Zero Values**: needs = 0, on_pms = 0, stock = 0
5. **Large Numbers**: needs = 1000, on_pms = 500, stock = 200

### Expected Results
- `order` selalu ≥ 0
- `over` selalu ≥ 0
- `order + over` = kebutuhan tambahan atau kelebihan
- Konsistensi data dengan formula yang ditentukan

## Maintenance

### Monitoring
- Log perhitungan untuk debugging
- Validasi nilai hasil perhitungan
- Alert jika ada nilai negatif yang tidak expected

### Future Enhancement
- Konfigurasi formula perhitungan
- Custom business rules untuk order dan over
- Integration dengan sistem procurement
- Reporting dan analytics untuk order dan over
