<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=destinos');
    exit;
}

if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['msg']      = 'Token de seguranca invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=destinos');
    exit;
}

$pdo = getConnection();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Destino invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=destinos');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM destinos WHERE id = :id');
$stmt->execute([':id' => $id]);

if (!$stmt->fetch()) {
    $_SESSION['msg']      = 'Destino nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
} else {
    try {
        $del = $pdo->prepare('DELETE FROM destinos WHERE id = :id');
        $del->execute([':id' => $id]);
        $_SESSION['msg']      = 'Destino excluido com sucesso!';
        $_SESSION['msg_tipo'] = 'success';
    } catch (Exception $e) {
        $_SESSION['msg']      = 'Erro ao excluir o destino. Verifique se nao ha pacotes vinculados.';
        $_SESSION['msg_tipo'] = 'danger';
    }
}

header('Location: index.php?page=destinos');
exit;
