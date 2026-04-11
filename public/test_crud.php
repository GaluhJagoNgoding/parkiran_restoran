<?php
/**
 * TEST CRUD - Verifikasi semua CRUD berfungsi
 */

require_once 'app/config/database.php';
require_once 'app/models/User.php';
require_once 'app/models/Kendaraan.php';
require_once 'app/models/Tarif.php';
require_once 'app/models/Area.php';
require_once 'app/models/Transaksi.php';
require_once 'app/models/Log.php';

$db = Database::connect();

echo "<html><head><title>Test CRUD</title><style>
body { font-family: Arial; margin: 20px; }
.test { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
.pass { background: #d4edda; color: #155724; }
.fail { background: #f8d7da; color: #721c24; }
h1 { color: #333; }
h2 { color: #0066cc; margin-top: 25px; }
</style></head><body>";

echo "<h1>🧪 Test CRUD Sistem Parkir Restoran</h1>";
echo "<hr>";

$total_test = 0;
$pass_test = 0;

function test($name, $result) {
    global $total_test, $pass_test;
    $total_test++;
    $class = $result ? 'pass' : 'fail';
    $status = $result ? '✓ PASS' : '✗ FAIL';
    echo "<div class='test $class'>$status : $name</div>";
    if ($result) $pass_test++;
}

// ===== TEST USER =====
echo "<h2>👤 TEST USER</h2>";

$all_users = User::all();
$user_count = mysqli_num_rows($all_users);
test("User::all() - Ambil semua user", $user_count > 0);

$user = User::find(1);
test("User::find(1) - Ambil user ID 1", $user != null);

$login_user = User::findByCredentials('admin', 'admin123', 'admin');
test("User::findByCredentials() - Login admin", $login_user != false);

// ===== TEST KENDARAAN =====
echo "<h2>🚗 TEST KENDARAAN</h2>";

$all_kendaraan = Kendaraan::all();
test("Kendaraan::all() - Database terhubung", $all_kendaraan != null);

// Test insert kendaraan
$insert_kendaraan = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) 
                     VALUES ('TEST1234', 'Motor', 'Hitam', 'Test User', 1)";
$result = mysqli_query($db, $insert_kendaraan);
test("INSERT Kendaraan", $result != false);

if ($result) {
    $kendaraan_id = mysqli_insert_id($db);
    
    // Test find
    $kendaraan = Kendaraan::find($kendaraan_id);
    test("Kendaraan::find(ID) - Ambil kendaraan", $kendaraan != null);
    
    // Test update
    $update_kendaraan = "UPDATE tb_kendaraan SET warna='Biru' WHERE id_kendaraan=$kendaraan_id";
    $result_update = mysqli_query($db, $update_kendaraan);
    test("UPDATE Kendaraan", $result_update != false);
    
    // Test delete
    $delete_kendaraan = "DELETE FROM tb_kendaraan WHERE id_kendaraan=$kendaraan_id";
    $result_delete = mysqli_query($db, $delete_kendaraan);
    test("DELETE Kendaraan", $result_delete != false);
}

// ===== TEST TARIF =====
echo "<h2>💰 TEST TARIF</h2>";

$all_tarif = Tarif::all();
$tarif_count = mysqli_num_rows($all_tarif);
test("Tarif::all() - Ambil semua tarif", $tarif_count > 0);

$tarif = Tarif::find(1);
test("Tarif::find(1) - Ambil tarif ID 1", $tarif != null);

// Test insert
$insert_tarif = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) 
                 VALUES ('Sepeda', 1000, 10000)";
$result = mysqli_query($db, $insert_tarif);
test("INSERT Tarif", $result != false);

if ($result) {
    $tarif_id = mysqli_insert_id($db);
    
    // Test delete
    $delete_tarif = "DELETE FROM tb_tarif WHERE id_tarif=$tarif_id";
    $result_delete = mysqli_query($db, $delete_tarif);
    test("DELETE Tarif", $result_delete != false);
}

// ===== TEST AREA =====
echo "<h2>📍 TEST AREA</h2>";

$all_area = Area::all();
$area_count = mysqli_num_rows($all_area);
test("Area::all() - Ambil semua area", $area_count > 0);

$area = Area::find(1);
test("Area::find(1) - Ambil area ID 1", $area != null);

// Test insert
$insert_area = "INSERT INTO tb_area (nama_area, kapasitas, lokasi) 
                VALUES ('Area Test', 50, 'Lokasi Test')";
$result = mysqli_query($db, $insert_area);
test("INSERT Area", $result != false);

if ($result) {
    $area_id = mysqli_insert_id($db);
    
    // Test delete
    $delete_area = "DELETE FROM tb_area WHERE id_area=$area_id";
    $result_delete = mysqli_query($db, $delete_area);
    test("DELETE Area", $result_delete != false);
}

// ===== TEST TRANSAKSI =====
echo "<h2>🎫 TEST TRANSAKSI</h2>";

$all_transaksi = Transaksi::all();
test("Transaksi::all() - Database terhubung", $all_transaksi != null);

// ===== TEST LOG =====
echo "<h2>📋 TEST LOG</h2>";

$all_log = Log::all();
test("Log::all() - Database terhubung", $all_log != null);

// Test insert log
$insert_log = "INSERT INTO tb_log_aktivitas (id_user, aktivitas) 
               VALUES (1, 'Test aktivitas')";
$result = mysqli_query($db, $insert_log);
test("INSERT Log", $result != false);

if ($result) {
    $log_id = mysqli_insert_id($db);
    
    // Test delete
    $delete_log = "DELETE FROM tb_log_aktivitas WHERE id_log=$log_id";
    $result_delete = mysqli_query($db, $delete_log);
    test("DELETE Log", $result_delete != false);
}

// ===== SUMMARY =====
echo "<hr><h2>📊 HASIL TEST</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px;'>";
echo "<strong>Total Test:</strong> $total_test<br>";
echo "<strong>PASS:</strong> <span style='color: green; font-weight: bold;'>$pass_test</span><br>";
echo "<strong>FAIL:</strong> <span style='color: red; font-weight: bold;'>" . ($total_test - $pass_test) . "</span><br>";

$percentage = ($pass_test / $total_test) * 100;
$status = $percentage == 100 ? "✅ SEMPURNA" : "⚠️ ADA ERROR";
echo "<strong>Status:</strong> <span style='font-weight: bold;'>$status ($percentage%)</span>";
echo "</div>";

echo "<hr>";
echo "<a href='index.php?url=auth/index' style='background: #2d6cdf; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 20px;'>
        ← Kembali ke Login
      </a>";

echo "</body></html>";

mysqli_close($db);
?>
