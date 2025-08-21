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

    $sqlAtivas = "SELECT m.*, u.nome AS remetente
            FROM mensagemTB m
            JOIN usuario u ON m.remetente_id = u.id
            WHERE m.destinatario_id = ? AND m.stat_us='ativo'
            ORDER BY m.data_envio DESC";
    $stmtAtivas = $conn->prepare($sqlAtivas);
    $stmtAtivas->bind_param("i", $id_usuario);
    $stmtAtivas->execute();
    $resultAtivas = $stmtAtivas->get_result();

    $sqlArquivadas = "SELECT m.*, u.nome AS remetente
                      FROM mensagemTB m
                      JOIN usuario u ON m.remetente_id = u.id
                      WHERE m.destinatario_id = ? AND m.stat_us='arquivado'
                      ORDER BY m.data_envio DESC";
    $stmtArquivadas = $conn->prepare($sqlArquivadas);
    $stmtArquivadas->bind_param("i", $id_usuario);
    $stmtArquivadas->execute();
    $resultArquivadas = $stmtArquivadas->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
    <a href="enviar.php">Enviar mensagem</a>
    <a href="logout.php">Sair</a>

    <h3>Mensagens Ativas</h3>
    <?php while ($msg = $resultAtivas->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; margin:5px; padding:10px;">
            <strong>De: </strong> <?php echo $msg['remetente']; ?><br>
            <strong>Título:</strong> <?php echo $msg['titulo']; ?><br>
            <strong>Mensagem:</strong> <?php echo $msg['mensagem']; ?><br>
            <em>Data: <?php echo $msg['data_envio']; ?></em><br>
            <a href="responder.php?id=<?php echo $msg['id']; ?>">Responder</a>
            <a href="arquivar.php?id=<?php echo $msg['id']; ?>">Arquivar</a>
        </div>
    <?php endwhile; ?>

    <h3>Mensagens Arquivadas</h3>
    <?php while ($msg = $resultArquivadas->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; margin:5px; padding:10px;">
            <strong>De: </strong> <?php echo $msg['remetente']; ?><br>
            <strong>Título:</strong> <?php echo $msg['titulo']; ?><br>
            <strong>Mensagem:</strong> <?php echo $msg['mensagem']; ?><br>
            <em>Data: <?php echo $msg['data_envio']; ?></em><br>
            <a href="desarquivar.php?id=<?php echo $msg['id']; ?>">Desarquivar</a>
        </div>
    <?php endwhile; ?>
</body>

</html>