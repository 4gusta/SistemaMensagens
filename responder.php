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
    $resposta_a = $_GET['id'] ?? null;

    if (!$resposta_a) {
        header("Location: painel.php");
        exit;
    }

    $sqlMsg = "SELECT m.*, u.nome AS remetente
               FROM mensagemTB m
               JOIN usuario u ON m.remetente_id = u.id
               WHERE m.id=?";
    $stmtMsg = $conn->prepare($sqlMsg);
    $stmtMsg->bind_param("i", $resposta_a);
    $stmtMsg->execute();
    $resultMsg = $stmtMsg->get_result();
    $mensagemOriginal = $resultMsg->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titulo = $_POST['titulo'];
        $mensagem = $_POST['mensagem'];
        $destinatario_id = $mensagemOriginal['remetente_id'];

        $sqlInsert = "INSERT INTO mensagemTB (remetente_id, destinatario_id, titulo, mensagem, resposta_a)
                      VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iissi", $id_usuario, $destinatario_id, $titulo, $mensagem, $resposta_a);

        if ($stmtInsert->execute()) {
            $msg = "Resposta enviada com sucesso!";
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "Erro ao enviar resposta " . $stmtInsert->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder mensagem</title>
</head>

<body>
    <div class="form-box">
        <h2>Responder a <?php echo htmlspecialchars($mensagemOriginal['remetente']); ?></h2>
        <p><strong>Mensagem original:</strong> <?php echo htmlspecialchars($mensagemOriginal['mensagem']); ?></p>
        <form method="POST">
            <input type="text" name="titulo" placeholder="Título resposta" required><br>
            <textarea name="mensagem" placeholder="Sua resposta" rows="5" required></textarea><br>
            <button type="submit">Responder</button>
            <button type="arquivar.php?id=<?php echo $mensagemOriginal['id']; ?>">Arquivar</button>
        </form>
        <p><a href="dashboard.php">Voltar ao dashboard</a></p>
        <?php if (!empty($msg))
            echo "<p class='msg'>$msg</p>"; ?>
    </div>

</body>

</html>