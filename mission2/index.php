<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily post</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 30px;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background-color: white;
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1a5edb;
        }

        .comment-section {
            max-width: 600px;
            margin: 30px auto;
        }

        .comment {
            background: white;
            border-left: 5px solid #2575fc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .comment .id {
            font-weight: bold;
            color: #555;
        }

        .comment .content {
            margin-top: 5px;
            color: #333;
        }

        .no-comment {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    <h1>💬 Daily post</h1>

    <?php
    $env = parse_ini_file('.env');
    $conn = new mysqli($env["SERVERNAME"], $env["USERNAME"], $env["PASSWORD"]);
    ?>

    <form action="/" method="post">
        <input type="text" name="comment" placeholder="Ajouter un post..." required>
        <button type="submit">Ajouter</button>
    </form>

    <div class="comment-section">
        <?php
        $sql = "USE {$env["DATABASE"]}";
        $conn->query($sql);

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $sql = "INSERT INTO comment (content) VALUES ('{$_POST["comment"]}')";
            $conn->query($sql);
        }

        $sql = "SELECT * FROM comment ORDER BY id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<div class='id'>#" . htmlspecialchars($row["id"]) . "</div>";
                echo "<div class='content'>" . htmlspecialchars($row["content"]) . "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-comment'>Aucun commentaire pour l'instant.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
