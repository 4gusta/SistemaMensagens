<?php
session_start();
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "mensagemBD";

$conn = new mysqli($hostname, $username, $password, $dbname);

$msg = "";

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);


} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM usuario WHERE nome = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "Usuário ou senha inválidos";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <div>
        <form method="POST">
            <h2>Login</h2>
            Nome: <input type="text" name="nome" placeholder="Nome de usuário " required><br>
            Senha: <input type="password" name="senha" placeholder="Senha" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Não possui uma conta? <a href="cadastro.php">Cadastre-se</a></p>
        <?php if (!empty($msg))
            echo "<p class='msg'>$msg</p>"; ?>
    </div>
</body>

</html>