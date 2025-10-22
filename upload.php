<?php
// === KONFIGURASI ===
$TARGET_DOMAIN = 'https://lanastore.my.id'; // Ganti dengan domain Anda
$UPLOAD_DIR = 'uploads/'; // Folder upload, relatif terhadap index.php
$MAX_FILE_SIZE = 200 * 1024 * 1024; // 200 MB
$ALLOWED_EXTENSIONS = [
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 
    'pdf', 'txt', 
    'zip', 'rar', 
    'mp3', 'wav', 
    'mp4', 'mkv', 'avi'
];
// ===================

$message = '';
$fileLink = '';
$messageType = 'error'; // Default-nya error
$publicUrlBase = rtrim($TARGET_DOMAIN, '/') . '/' . rtrim($UPLOAD_DIR, '/') . '/';

// 1. Logika Pemrosesan Upload File (Saat form di-submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] != UPLOAD_ERR_NO_FILE) {
        
        $file = $_FILES["fileToUpload"];

        // Cek error bawaan PHP
        if ($file["error"] !== UPLOAD_ERR_OK) {
            switch ($file["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "Error: File terlalu besar. Batas maksimal adalah 200MB (cek juga pengaturan 'upload_max_filesize' di server Anda).";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = "Error: File hanya ter-upload sebagian.";
                    break;
                default:
                    $message = "Error: Terjadi kesalahan saat upload. Kode: " . $file["error"];
                    break;
            }
        } else {
            // 2. Cek Ukuran File (Validasi sisi server)
            if ($file["size"] > $MAX_FILE_SIZE) {
                $message = "Error: File terlalu besar. Batas maksimal adalah 200MB.";
            } else {
                // 3. Cek Ekstensi File
                $filename = basename($file["name"]);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($extension, $ALLOWED_EXTENSIONS)) {
                    $message = "Error: Tipe file tidak diizinkan. (Hanya: " . implode(', ', $ALLOWED_EXTENSIONS) . ")";
                } else {
                    // 4. Buat Nama File Unik
                    $randomString = bin2hex(random_bytes(8)); // 16 karakter acak
                    $newFilename = time() . '_' . $randomString . '.' . $extension;
                    $targetFile = $UPLOAD_DIR . $newFilename;

                    // 5. Pindahkan File
                    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                        $message = "File berhasil diupload!";
                        $fileLink = $publicUrlBase . $newFilename;
                        $messageType = 'success'; // Set jadi sukses
                    } else {
                        $message = "Error: Gagal memindahkan file ke folder tujuan. Pastikan folder 'uploads' writable (permission 755 atau 777).";
                    }
                }
            }
        }
    } else {
        $message = "Error: Tidak ada file yang dipilih untuk diupload.";
    }
} else {
    // Jika diakses langsung tanpa POST, redirect ke index.html
    header("Location: index.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Upload - LanaStore</title>
    <!-- Menggunakan CSS yang sama dari index.html -->
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
            text-align: center;
        }
        .message {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background-color: #e6f7ec;
            color: #006421;
            border: 1px solid #b7ebc9;
        }
        .message.error {
            background-color: #fdecea;
            color: #a91b0d;
            border: 1px solid #f9c6c2;
        }
        .file-link {
            text-align: center;
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .file-link input[type="text"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-family: "Courier New", Courier, monospace;
            font-size: 14px;
            background-color: #fff;
            color: #495057;
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
            margin: 5px;
        }
        .nav-links a:hover {
            background-color: #0056b3;
        }
        .nav-links a.secondary {
            background-color: #6c757d;
        }
        .nav-links a.secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Status Upload</h1>

        <!-- 1. Tampilkan Pesan Hasil Upload -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- 2. Tampilkan Link File Jika Sukses -->
        <?php if ($fileLink): ?>
            <div class="file-link">
                <strong>Link File:</strong>
                <input type="text" value="<?php echo htmlspecialchars($fileLink); ?>" readonly onclick="this.select();">
            </div>
        <?php endif; ?>

        <!-- 3. Tombol Navigasi -->
        <div class="nav-links">
            <a href="index.html">Upload Lagi</a>
            <a href="list.php" class="secondary">Lihat Daftar File</a>
        </div>

    </div>

</body>
</html>
