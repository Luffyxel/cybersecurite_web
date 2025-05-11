<?php
session_start();
// Génère une question captcha si besoin
if (empty($_SESSION['captcha_a']) || empty($_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = rand(1, 10);
    $_SESSION['captcha_b'] = rand(1, 10);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(241, 144, 52),rgb(252, 37, 37));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 12px;
            border: none;
            background-color:rgb(34, 163, 141);
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #1a5edb;
        }

        .error {
            color: red;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="/" method="post">
        <h2 style="text-align:center;">Connexion</h2>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <label>
            Combien font <?php echo $_SESSION['captcha_a'] . " + " . $_SESSION['captcha_b']; ?> ?
            <input type="text" name="captcha" required>
        </label>
        <button type="submit">Se connecter</button>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $captcha_ok = false;
            if (isset($_POST['captcha'])) {
                $expected = $_SESSION['captcha_a'] + $_SESSION['captcha_b'];
                if ((int)$_POST['captcha'] === $expected) {
                    $captcha_ok = true;
                } else {
                    echo "<div class='error'>Captcha incorrect</div>";
                }
                // Regénère une nouvelle question pour la prochaine tentative
                $_SESSION['captcha_a'] = rand(1, 10);
                $_SESSION['captcha_b'] = rand(1, 10);
            }

            if ($captcha_ok) {
                $env = parse_ini_file('.env');
                $conn = new mysqli($env["SERVERNAME"], $env["USERNAME"], $env["PASSWORD"]);

                if ($_POST["email"] && $_POST["password"]) {
                    $sql = "USE {$env["DATABASE"]}";
                    $conn->query($sql);
                    $sql = "SELECT * FROM user WHERE email='{$_POST["email"]}' AND password='{$_POST["password"]}'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            header("Location: profil.php?id={$row["id"]}");
                        }
                        exit();
                    } else {
                        echo "<div class='error'>Email ou mot de passe incorrect</div>";
                    }
                }
                $conn->close();
            }
        }
        ?>
    </form>
</body>
</html>
