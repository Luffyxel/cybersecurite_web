<?php
session_start();
// Génère un token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
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
            background-color: #2575fc;
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

        .success {
            color: green;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <form action="/" method="post">
        <h2 style="text-align:center;">Inscription</h2>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Créer un compte</button>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Vérifie le token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo "<div class='error'>Token CSRF invalide</div>";
            } else {
                $env = parse_ini_file('.env');
                $conn = new mysqli($env["SERVERNAME"], $env["USERNAME"], $env["PASSWORD"], $env["DATABASE"]);

                if ($conn->connect_error) {
                    die("<div class='error'>Erreur de connexion à la base</div>");
                }

                if (!empty($_POST["email"]) && !empty($_POST["password"])) {
                    $email = $_POST["email"];
                    $password = $_POST["password"];

                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO user (email, password) VALUES ('$email', '$hashedPassword')";
                    if ($conn->query($sql) === TRUE) {

                        $userId = $conn->insert_id;

                        header("Location: profil.php?id=$userId");
                        exit();
                    } else {
                        echo "<div class='error'>Erreur lors de l'inscription</div>";
                    }
                } else {
                    echo "<div class='error'>Tous les champs sont requis</div>";
                }

                $conn->close();
            }
        }
        ?>
    </form>
</body>

</html>
