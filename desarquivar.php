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
    $id_mensagem = $_GET['id'] ?? null;

    if ($id_mensagem) {
        $sql = "UPDATE mensagemTB SET stat_us='ativo' WHERE id=? AND destinatario_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_mensagem, $id_usuario);
        $stmt->execute();
    }
    header("Location: dashboard.php");
    exit;
}



?>