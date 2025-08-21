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
    if (!isset($_SESSION['id'])) {
        header("Location: index.php");
        exit;
    }

    $id_usuario = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $destinatario_nome = $_POST['destinatario'];
        $titulo = $_POST['titulo'];
        $mensagem = $_POST['mensagem'];

        $sqlUser = "SELECT id FROM usuario WHERE nome = ?";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bind_param("s", $destinatario_nome);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();
        $destinatario = $resultUser->fetch_assoc();

        if ($destinatario) {
            $sql = "INSERT INTO mensagemTB (remetente_id, destinatario_id, titulo, mensagem) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $id_usuario, $destinatario['id'], $titulo, $mensagem);
            if ($stmt->execute()) {
                $msg = "Mensagem enviada com sucesso!";
            } else {
                $msg = "Erro ao enviar mensagem: " . $stmt->error;
            }
        } else {
            $msg = "Destinatário não encontrado!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem</title>
</head>

<body>
    <div class="form-box">
        <h2>Enviar Mensagem</h2>
        <form method="POST">
            <input type="text" name="destinatario" placeholder="Nome do destinatario" required><br>
            <input type="text" name="titulo" placeholder="Título" required><br>
            <textarea name="mensagem" placeholder="Mensagem" rows="5" required></textarea><br>
            <button type="submit">Enviar</button>
        </form>
        <p><a href="dashboard.php">Voltar ao painel</a></p>
        <?php if (!empty($msg))
            echo "<p class='msg'>$msg</p>"; ?>
    </div>
</body>

</html>