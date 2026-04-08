<?php
// Redirect ke public front controller agar website bisa diakses langsung dari domain root.
// Tambahkan route default agar langsung masuk ke halaman login.
header('Location: public/index.php?url=auth/index');
exit;
