<?php
$pdo = getConnection();

$msg     = $_SESSION['msg']      ?? null;
$msgTipo = $_SESSION['msg_tipo'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_tipo']);

$reservas = $pdo->query(
    'SELECT r.*, c.nome AS cliente_nome, p.nome AS pacote_nome
     FROM reservas r
     LEFT JOIN clientes c ON c.id = r.cliente_id
     LEFT JOIN pacotes  p ON p.id = r.pacote_id
     ORDER BY r.id DESC'
)->fetchAll();

$statusLabels = [
    'pendente'   => 'Pendente',
    'confirmada' => 'Confirmada',
    'cancelada'  => 'Cancelada',
];
?>

<div class="container">
    <div class="flex-between mb-16">
        <h2>Reservas</h2>
        <a href="index.php?page=reservas_criar" class="btn btn-primary">Nova Reserva</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($reservas)): ?>
        <p>Nenhuma reserva cadastrada.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Pacote</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Valor Pago (R$)</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['cliente_nome'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['pacote_nome'] ?? '—') ?></td>
                            <td><?= date('d/m/Y', strtotime($r['data_reserva'])) ?></td>
                            <td><?= htmlspecialchars($statusLabels[$r['status']] ?? $r['status']) ?></td>
                            <td><?= number_format($r['valor_pago'], 2, ',', '.') ?></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?page=reservas_editar&id=<?= $r['id'] ?>" class="btn btn-secondary">Editar</a>
                                    <form method="POST" action="index.php?page=reservas_deletar" style="display:inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        <?= csrfInput() ?>
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
