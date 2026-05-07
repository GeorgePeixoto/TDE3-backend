<?php
$pdo = getConnection();

$msg     = $_SESSION['msg']      ?? null;
$msgTipo = $_SESSION['msg_tipo'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_tipo']);

$clientes = $pdo->query('SELECT * FROM clientes ORDER BY id DESC')->fetchAll();
?>

<div class="container">
    <div class="flex-between mb-16">
        <h2>Clientes</h2>
        <a href="index.php?page=clientes_criar" class="btn btn-primary">Novo Cliente</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
        <p>Nenhum cliente cadastrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CPF</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['telefone']) ?></td>
                            <td><?= htmlspecialchars(preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $c['cpf'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?page=clientes_editar&id=<?= $c['id'] ?>" class="btn btn-secondary">Editar</a>
                                    <form method="POST" action="index.php?page=clientes_deletar" style="display:inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        <?= csrfInput() ?>
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
