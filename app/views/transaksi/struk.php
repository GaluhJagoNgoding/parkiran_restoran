<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Struk Parkir - <?= $transaksi['id_parkir'] ?? '' ?></title>
	<style>
		body{font-family:Arial,Helvetica,sans-serif;background:#f4f6fb;padding:20px}
		.receipt{max-width:400px;margin:0 auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.06)}
		.brand{font-weight:800;color:#2d6cdf;margin-bottom:6px}
		.meta{font-size:12px;color:#666;margin-bottom:12px}
		.rows{width:100%;border-top:1px dashed #e6e6e6;padding-top:8px}
		.row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f1f1f1}
		.total{font-weight:800;font-size:20px;margin-top:12px;text-align:right}
		.actions{display:flex;gap:8px;margin-top:14px}
		.btn{flex:1;padding:8px 10px;border-radius:6px;border:none;cursor:pointer;font-weight:600}
		.btn.print{background:#2d6cdf;color:#fff}
		.btn.download{background:#4caf50;color:#fff}
		.back-btn{background:#95a5a6;color:#fff;text-decoration:none;display:block;margin-top:8px;padding:8px 10px;border-radius:6px;text-align:center;font-weight:600}
		.back-btn:hover{background:#7f8c8d}
		@media print{.actions,.back-btn{display:none}}
		pre{white-space:pre-wrap;font-family:inherit}
	</style>
</head>
<body>
	<div class="receipt" id="receipt">
		<div class="brand">Parkir Restoran</div>
		<div class="meta">Struk ID: <?= htmlspecialchars($transaksi['id_parkir'] ?? '') ?> • Petugas: <?= htmlspecialchars($_SESSION['user']['nama'] ?? ($_SESSION['user']['username'] ?? '-')) ?></div>

		<div class="rows">
			<div class="row"><div>Plat</div><div><?= htmlspecialchars($transaksi['plat_nomor'] ?? '-') ?></div></div>
			<div class="row"><div>Jenis</div><div><?= htmlspecialchars($transaksi['jenis_kendaraan'] ?? '-') ?></div></div>
			<div class="row"><div>Area</div><div><?= htmlspecialchars($transaksi['id_area'] ?? '-') ?></div></div>
			<div class="row"><div>Waktu Masuk</div><div><?= $transaksi['waktu_masuk'] ?? '-' ?></div></div>
			<div class="row"><div>Waktu Keluar</div><div><?= $transaksi['waktu_keluar'] ?? '-' ?></div></div>
			<div class="row"><div>Status</div><div><span style="background-color: #27ae60; color: white; padding: 6px 12px; border-radius: 3px; font-weight: bold; font-size: 14px;">Selesai</span></div></div>
			<div class="row"><div>Biaya</div><div>Rp <?= number_format($transaksi['biaya_total'] ?? 0, 0, ',', '.') ?></div></div>
		</div>

		<div class="total">Rp <?= number_format($transaksi['biaya_total'] ?? 0, 0, ',', '.') ?></div>

		<div class="actions">
			<button class="btn print" onclick="window.print()">Cetak</button>
			<button class="btn download" id="downloadBtn">Download</button>
		</div>
		<a href="index.php?url=transaksi/index" class="back-btn">← Kembali ke Daftar Transaksi</a>
	</div>

	<script>
		document.getElementById('downloadBtn').addEventListener('click', function(){
			var el = document.getElementById('receipt');
			var html = '<!doctype html><html>'+document.head.outerHTML+'<body>' + el.outerHTML + '</body></html>';
			var blob = new Blob([html], {type: 'text/html'});
			var a = document.createElement('a');
			a.href = URL.createObjectURL(blob);
			a.download = 'struk_<?= $transaksi['id_parkir'] ?? 'transaksi' ?>.html';
			document.body.appendChild(a);
			a.click();
			a.remove();
		});
	</script>
</body>
</html>

