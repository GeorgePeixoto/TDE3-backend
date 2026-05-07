<?php
$pdo = getConnection();

$msg     = $_SESSION['msg']      ?? null;
$msgTipo = $_SESSION['msg_tipo'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_tipo']);

$pacotes = $pdo->query(
    'SELECT p.*, d.nome AS destino_nome
     FROM pacotes p
     LEFT JOIN destinos d ON d.id = p.destino_id
     ORDER BY p.id DESC'
)->fetchAll();
?>

<div class="container">
    <div class="flex-between mb-16">
        <h2>Pacotes de Viagem</h2>
        <a href="index.php?page=pacotes_criar" class="btn btn-primary">Novo Pacote</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($pacotes)): ?>
        <p>Nenhum pacote cadastrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Destino</th>
                        <th>Duracao (dias)</th>
                        <th>Preco (R$)</th>
                        <th>Vagas</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacotes as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars($p['destino_nome'] ?? '—') ?></td>
                            <td><?= $p['duracao_dias'] ?></td>
                            <td><?= number_format($p['preco'], 2, ',', '.') ?></td>
                            <td><?= $p['vagas_disponiveis'] ?></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?page=pacotes_editar&id=<?= $p['id'] ?>" class="btn btn-secondary">Editar</a>
                                    <form method="POST" action="index.php?page=pacotes_deletar" style="display:inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        <?= csrfInput() ?>
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
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
