<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=pacotes');
    exit;
}

if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['msg']      = 'Token de seguranca invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=pacotes');
    exit;
}

$pdo = getConnection();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Pacote invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=pacotes');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM pacotes WHERE id = :id');
$stmt->execute([':id' => $id]);

if (!$stmt->fetch()) {
    $_SESSION['msg']      = 'Pacote nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
} else {
    try {
        $del = $pdo->prepare('DELETE FROM pacotes WHERE id = :id');
        $del->execute([':id' => $id]);
        $_SESSION['msg']      = 'Pacote excluido com sucesso!';
        $_SESSION['msg_tipo'] = 'success';
    } catch (Exception $e) {
        $_SESSION['msg']      = 'Erro ao excluir o pacote. Verifique se nao ha reservas vinculadas.';
        $_SESSION['msg_tipo'] = 'danger';
    }
}

header('Location: index.php?page=pacotes');
exit;
