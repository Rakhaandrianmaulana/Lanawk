<?php
// === KONFIGURASI (Harus sama) ===
$TARGET_DOMAIN = 'https://lanastore.my.id';
$UPLOAD_DIR = 'uploads/';
// ===================

$publicUrlBase = rtrim($TARGET_DOMAIN, '/') . '/' . rtrim($UPLOAD_DIR, '/') . '/';

// Fungsi untuk membaca daftar file
function getUploadedFiles($uploadDir, $publicUrlBase) {
    $fileList = [];
    // File yang diabaikan (tidak ditampilkan di list)
    $ignoredFiles = ['.', '..', '.htaccess', '.gitkeep'];

    if (is_dir($uploadDir) && is_readable($uploadDir)) {
        $files = scandir($uploadDir);
        foreach ($files as $file) {
            if (!in_array($file, $ignoredFiles)) {
                $fileList[] = [
                    'name' => htmlspecialchars($file),
                    'url' => $publicUrlBase . $file
                ];
            }
        }
    }
    // Urutkan dari yang terbaru (berdasarkan nama file timestamp)
    rsort($fileList);
    return $fileList;
}

$uploadedFiles = getUploadedFiles($UPLOAD_DIR, $publicUrlBase);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File - LanaStore</title>
    <!-- Menggunakan CSS yang sama -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
            padding: 30px;
            width: 100%;
            max-width: 700px;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            color: #1a1a1a;
            margin-top: 0;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-list ul {
            list-style-type: none;
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .file-list li {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .file-list li:last-child {
            border-bottom: none;
        }
        .file-list a {
            text-decoration: none;
            color: #0056b3;
            word-break: break-all;
        }
        .file-list a:hover {
            text-decoration: underline;
        }
        .nav-links {
            text-align: center;
            margin-top: 25px;
        }
        .nav-links a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .nav-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Daftar File Terupload (<?php echo count($uploadedFiles); ?>)</h1>
        
        <div class="nav-links" style="margin-bottom: 25px;">
            <a href="index.html">Kembali ke Halaman Upload</a>
        </div>

        <div class="file-list">
            <?php if (empty($uploadedFiles)): ?>
                <p style="text-align: center; color: #777;">Belum ada file yang diupload.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($uploadedFiles as $file): ?>
                        <li>
                            <a href="<?php echo $file['url']; ?>" target="_blank">
                                <?php echo $file['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
