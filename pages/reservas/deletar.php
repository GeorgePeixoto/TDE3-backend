<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=reservas');
    exit;
}

if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['msg']      = 'Token de seguranca invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=reservas');
    exit;
}

$pdo = getConnection();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Reserva invalida.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=reservas');
    exit;
}

$stmt = $pdo->prepare('SELECT status, pacote_id FROM reservas WHERE id = :id');
$stmt->execute([':id' => $id]);
$reserva = $stmt->fetch();

if (!$reserva) {
    $_SESSION['msg']      = 'Reserva nao encontrada.';
    $_SESSION['msg_tipo'] = 'danger';
} else {
    $pdo->beginTransaction();
    try {
        $del = $pdo->prepare('DELETE FROM reservas WHERE id = :id');
        $del->execute([':id' => $id]);

        if ($reserva['status'] !== 'cancelada') {
            $stmt = $pdo->prepare('UPDATE pacotes SET vagas_disponiveis = vagas_disponiveis + 1 WHERE id = :id');
            $stmt->execute([':id' => (int) $reserva['pacote_id']]);
        }

        $pdo->commit();
        $_SESSION['msg']      = 'Reserva excluida com sucesso!';
        $_SESSION['msg_tipo'] = 'success';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg']      = 'Erro ao excluir a reserva. Tente novamente.';
        $_SESSION['msg_tipo'] = 'danger';
    }
}

header('Location: index.php?page=reservas');
exit;
