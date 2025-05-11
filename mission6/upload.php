<?php
$uploadDir = 'uploads/';
$message = '';

// Crée le dossier d'upload s'il n'existe pas
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gestion de l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] === 0) {
        $filename = basename($_FILES['myfile']['name']);
        $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $targetFile = $uploadDir . $filename;

        // Interdit les fichiers PHP et assimilés
        $forbidden = ['php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8'];
        if (in_array($fileType, $forbidden)) {
            $message = "❌ Upload interdit : les fichiers PHP ne sont pas autorisés.";
        } else {
            // Optionnel : vérifier le type MIME pour plus de sécurité
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['myfile']['tmp_name']);
            finfo_close($finfo);

            // Liste blanche d'extensions autorisées (exemple : images et txt)
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'txt', 'pdf'];
            if (!in_array($fileType, $allowed)) {
                $message = "❌ Seuls les fichiers images et txt/pdf sont autorisés.";
            } else {
                if (move_uploaded_file($_FILES['myfile']['tmp_name'], $targetFile)) {
                    $message = "✅ File uploaded successfully: " . htmlspecialchars($filename);
                } else {
                    $message = "❌ Error: Could not move uploaded file.";
                }
            }
        }
    } else {
        $message = "❌ Upload error: " . $_FILES['myfile']['error'];
    }
}

// Liste des fichiers
$files = array_diff(scandir($uploadDir), ['.', '..']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>PHP Drive - Upload Result</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .message {
            background: #f5f5f5;
            padding: 10px;
            border-left: 4px solid #3498db;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fafafa;
            margin: 8px 0;
            padding: 10px;
            border-left: 4px solid #2ecc71;
            border-radius: 5px;
            word-break: break-all;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📁 PHP Drive</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <h3>Uploaded Files:</h3>
        <?php if (empty($files)) : ?>
            <p>No files found in the upload folder.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($files as $file) : ?>
                    <li><?php echo htmlspecialchars($file); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="back-link" href="index.php">&larr; Back to Upload Page</a>
    </div>
</body>

</html>