<?php
// Test file untuk memverifikasi routing inv_week
echo "<h1>Test Inv Week Routing</h1>";

// Test URL yang seharusnya bekerja
$base_url = 'http://localhost/cdummy/inventory/data/data_inv_week_show/2024?month=12';
echo "<p><strong>Test URL:</strong> <a href='$base_url' target='_blank'>$base_url</a></p>";

// Test dengan tahun dan bulan yang berbeda
$test_cases = [
    ['year' => 2024, 'month' => 1],
    ['year' => 2024, 'month' => 6],
    ['year' => 2024, 'month' => 12],
    ['year' => 2023, 'month' => 12]
];

echo "<h2>Test Cases:</h2>";
foreach ($test_cases as $test) {
    $url = "http://localhost/cdummy/inventory/data/data_inv_week_show/{$test['year']}?month={$test['month']}";
    echo "<p><a href='$url' target='_blank'>Year: {$test['year']}, Month: {$test['month']}</a></p>";
}

echo "<h2>Debug Info:</h2>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Test apakah file data_inv_week_show.php ada
$view_file = 'application/views/report/week/data_inv_week_show.php';
if (file_exists($view_file)) {
    echo "<p style='color: green;'>✓ File $view_file exists</p>";
} else {
    echo "<p style='color: red;'>✗ File $view_file NOT found</p>";
}

// Test apakah controller inventory.php ada
$controller_file = 'application/controllers/inventory.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✓ File $controller_file exists</p>";
} else {
    echo "<p style='color: red;'>✗ File $controller_file NOT found</p>";
}

// Test apakah model report_model.php ada
$model_file = 'application/models/report_model.php';
if (file_exists($model_file)) {
    echo "<p style='color: green;'>✓ File $model_file exists</p>";
} else {
    echo "<p style='color: red;'>✗ File $model_file NOT found</p>";
}
?>
