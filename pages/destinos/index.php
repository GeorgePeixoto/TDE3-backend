<?php
$pdo = getConnection();

$msg     = $_SESSION['msg']      ?? null;
$msgTipo = $_SESSION['msg_tipo'] ?? 'success';
unset($_SESSION['msg'], $_SESSION['msg_tipo']);

$destinos = $pdo->query('SELECT * FROM destinos ORDER BY id DESC')->fetchAll();
?>

<div class="container">
    <div class="flex-between mb-16">
        <h2>Destinos</h2>
        <a href="index.php?page=destinos_criar" class="btn btn-primary">Novo Destino</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= htmlspecialchars($msgTipo) ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($destinos)): ?>
        <p>Nenhum destino cadastrado.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Pais</th>
                        <th>Clima</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinos as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td><?= htmlspecialchars($d['nome']) ?></td>
                            <td><?= htmlspecialchars($d['pais']) ?></td>
                            <td><?= htmlspecialchars($d['clima']) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?page=destinos_editar&id=<?= $d['id'] ?>" class="btn btn-secondary">Editar</a>
                                    <form method="POST" action="index.php?page=destinos_deletar" style="display:inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        <?= csrfInput() ?>
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
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
