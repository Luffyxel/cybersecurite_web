<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
    </style>
</head>
<body>
    <form action="/" method="post">
        <h2 style="text-align:center;">Connexion</h2>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $env = parse_ini_file('.env');
            $conn = new mysqli($env["SERVERNAME"], $env["USERNAME"], $env["PASSWORD"]);

        if (!empty($_POST["email"]) && !empty($_POST["password"])) {
            $email = $_POST["email"];
            $password = $_POST["password"];

            $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    header('Location: dashboard.php');
                    exit();
                } else {
                    echo "<div class='error'>Email ou mot de passe incorrect</div>";
                }
            }
            $conn->close();
        }
        ?>
    </form>
</body>
</html>
