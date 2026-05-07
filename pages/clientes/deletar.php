<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=clientes');
    exit;
}

if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['msg']      = 'Token de seguranca invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=clientes');
    exit;
}

$pdo = getConnection();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Cliente invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=clientes');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM clientes WHERE id = :id');
$stmt->execute([':id' => $id]);

if (!$stmt->fetch()) {
    $_SESSION['msg']      = 'Cliente nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
} else {
    try {
        $del = $pdo->prepare('DELETE FROM clientes WHERE id = :id');
        $del->execute([':id' => $id]);
        $_SESSION['msg']      = 'Cliente excluido com sucesso!';
        $_SESSION['msg_tipo'] = 'success';
    } catch (Exception $e) {
        $_SESSION['msg']      = 'Erro ao excluir o cliente. Verifique se nao ha reservas vinculadas.';
        $_SESSION['msg_tipo'] = 'danger';
    }
}

header('Location: index.php?page=clientes');
exit;
