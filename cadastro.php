<?php
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
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario(nome, senha) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $senha);

        if ($stmt->execute()) {
            $msg = "Usuário cadastrado com sucesso! <a href='index.php'>Fazer login</a>";
        } else {
            $msg = "Erro" . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>

<body>
    <div>
        <form method="POST">
            <h2>Cadastro</h2>
            Nome: <input type="text" name="nome" placeholder="Nome de usuário" required><br>
            Senha: <input type="password" name="senha" placeholder="Senha" required><br>
            <button type="submit">Cadastrar</button>
        </form>
        <p>Já possui uma conta? <a href="index.php">Login</a></p>
        <?php if (!empty($msg))
            echo "<p class='msg'>$msg</p>"; ?>
    </div>
</body>

</html>