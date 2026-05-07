<?php
$pdo = getConnection();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Cliente invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=clientes');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM clientes WHERE id = :id');
$stmt->execute([':id' => $id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    $_SESSION['msg']      = 'Cliente nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=clientes');
    exit;
}

$erros = [];
$nome     = $cliente['nome'];
$email    = $cliente['email'];
$telefone = $cliente['telefone'];
$cpf      = $cliente['cpf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        $erros['csrf'] = 'Token de seguranca invalido. Reenvie o formulario.';
    }

    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cpf      = preg_replace('/\D/', '', $_POST['cpf'] ?? '');

    if ($nome === '')     $erros['nome']     = 'O campo Nome e obrigatorio.';
    if ($email === '')    $erros['email']    = 'O campo Email e obrigatorio.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                          $erros['email']    = 'Email invalido.';
    if ($telefone === '') $erros['telefone'] = 'O campo Telefone e obrigatorio.';
    if ($cpf === '')      $erros['cpf']      = 'O campo CPF e obrigatorio.';
    elseif (!validarCpf($cpf))
                          $erros['cpf']      = 'CPF invalido.';

    if (empty($erros)) {
        $stmt = $pdo->prepare('SELECT id FROM clientes WHERE email = :email AND id != :id');
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) {
            $erros['email'] = 'Ja existe outro cliente com este email.';
        }

        $stmt = $pdo->prepare('SELECT id FROM clientes WHERE cpf = :cpf AND id != :id');
        $stmt->execute([':cpf' => $cpf, ':id' => $id]);
        if ($stmt->fetch()) {
            $erros['cpf'] = 'Ja existe outro cliente com este CPF.';
        }
    }

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, cpf = :cpf WHERE id = :id'
            );
            $stmt->execute([
                ':nome'     => $nome,
                ':email'    => $email,
                ':telefone' => $telefone,
                ':cpf'      => $cpf,
                ':id'       => $id,
            ]);

            $_SESSION['msg']      = 'Cliente atualizado com sucesso!';
            $_SESSION['msg_tipo'] = 'success';
            header('Location: index.php?page=clientes');
            exit;
        } catch (Exception $e) {
            $erros['db'] = 'Erro ao atualizar o cliente. Tente novamente.';
        }
    }
}
?>

<div class="container">
    <h2>Editar Cliente</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">Corrija os erros abaixo para continuar.</div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="index.php?page=clientes_editar&id=<?= $id ?>">
            <?= csrfInput() ?>
            <div class="form-group <?= isset($erros['nome']) ? 'has-error' : '' ?>">
                <label for="nome">Nome <span class="required">*</span></label>
                <input type="text" id="nome" name="nome" maxlength="150" value="<?= htmlspecialchars($nome) ?>">
                <?php if (isset($erros['nome'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['nome']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['email']) ? 'has-error' : '' ?>">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" maxlength="200" value="<?= htmlspecialchars($email) ?>">
                <?php if (isset($erros['email'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['email']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['telefone']) ? 'has-error' : '' ?>">
                <label for="telefone">Telefone <span class="required">*</span></label>
                <input type="text" id="telefone" name="telefone" maxlength="20" value="<?= htmlspecialchars($telefone) ?>">
                <?php if (isset($erros['telefone'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['telefone']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['cpf']) ? 'has-error' : '' ?>">
                <label for="cpf">CPF <span class="required">*</span></label>
                <input type="text" id="cpf" name="cpf" maxlength="14" value="<?= htmlspecialchars($cpf) ?>">
                <span class="field-hint">Pode digitar com ou sem pontuacao.</span>
                <?php if (isset($erros['cpf'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['cpf']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="index.php?page=clientes" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
