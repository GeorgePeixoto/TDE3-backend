<?php
$pdo = getConnection();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Destino invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=destinos');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM destinos WHERE id = :id');
$stmt->execute([':id' => $id]);
$destino = $stmt->fetch();

if (!$destino) {
    $_SESSION['msg']      = 'Destino nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=destinos');
    exit;
}

$erros = [];
$nome      = $destino['nome'];
$pais      = $destino['pais'];
$descricao = $destino['descricao'];
$clima     = $destino['clima'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        $erros['csrf'] = 'Token de seguranca invalido. Reenvie o formulario.';
    }

    $nome      = trim($_POST['nome'] ?? '');
    $pais      = trim($_POST['pais'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $clima     = trim($_POST['clima'] ?? '');

    if ($nome === '')      $erros['nome']      = 'O campo Nome e obrigatorio.';
    if ($pais === '')      $erros['pais']       = 'O campo Pais e obrigatorio.';
    if ($descricao === '') $erros['descricao']  = 'O campo Descricao e obrigatorio.';
    if ($clima === '')     $erros['clima']      = 'O campo Clima e obrigatorio.';

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare('UPDATE destinos SET nome = :nome, pais = :pais, descricao = :descricao, clima = :clima WHERE id = :id');
            $stmt->execute([
                ':nome'      => $nome,
                ':pais'      => $pais,
                ':descricao' => $descricao,
                ':clima'     => $clima,
                ':id'        => $id,
            ]);

            $_SESSION['msg']      = 'Destino atualizado com sucesso!';
            $_SESSION['msg_tipo'] = 'success';
            header('Location: index.php?page=destinos');
            exit;
        } catch (Exception $e) {
            $erros['db'] = 'Erro ao atualizar o destino. Tente novamente.';
        }
    }
}
?>

<div class="container">
    <h2>Editar Destino</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">Corrija os erros abaixo para continuar.</div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="index.php?page=destinos_editar&id=<?= $id ?>">
            <?= csrfInput() ?>
            <div class="form-group <?= isset($erros['nome']) ? 'has-error' : '' ?>">
                <label for="nome">Nome <span class="required">*</span></label>
                <input type="text" id="nome" name="nome" maxlength="150" value="<?= htmlspecialchars($nome) ?>">
                <?php if (isset($erros['nome'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['nome']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['pais']) ? 'has-error' : '' ?>">
                <label for="pais">Pais <span class="required">*</span></label>
                <input type="text" id="pais" name="pais" maxlength="100" value="<?= htmlspecialchars($pais) ?>">
                <?php if (isset($erros['pais'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['pais']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['descricao']) ? 'has-error' : '' ?>">
                <label for="descricao">Descricao <span class="required">*</span></label>
                <textarea id="descricao" name="descricao" rows="4"><?= htmlspecialchars($descricao) ?></textarea>
                <?php if (isset($erros['descricao'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['descricao']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['clima']) ? 'has-error' : '' ?>">
                <label for="clima">Clima <span class="required">*</span></label>
                <input type="text" id="clima" name="clima" maxlength="100" value="<?= htmlspecialchars($clima) ?>">
                <?php if (isset($erros['clima'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['clima']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="index.php?page=destinos" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
