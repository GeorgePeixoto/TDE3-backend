<?php
$pdo = getConnection();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Pacote invalido.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=pacotes');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM pacotes WHERE id = :id');
$stmt->execute([':id' => $id]);
$pacote = $stmt->fetch();

if (!$pacote) {
    $_SESSION['msg']      = 'Pacote nao encontrado.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=pacotes');
    exit;
}

$destinos = $pdo->query('SELECT id, nome FROM destinos ORDER BY nome')->fetchAll();

$erros = [];
$nome              = $pacote['nome'];
$destino_id        = $pacote['destino_id'];
$duracao_dias      = $pacote['duracao_dias'];
$preco             = $pacote['preco'];
$vagas_disponiveis = $pacote['vagas_disponiveis'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        $erros['csrf'] = 'Token de seguranca invalido. Reenvie o formulario.';
    }

    $nome              = trim($_POST['nome'] ?? '');
    $destino_id        = $_POST['destino_id'] ?? '';
    $duracao_dias      = $_POST['duracao_dias'] ?? '';
    $preco             = $_POST['preco'] ?? '';
    $vagas_disponiveis = $_POST['vagas_disponiveis'] ?? '';

    if ($nome === '')                                                           $erros['nome']       = 'O campo Nome e obrigatorio.';
    if (!filter_var($destino_id, FILTER_VALIDATE_INT))                          $erros['destino_id'] = 'Selecione um destino valido.';
    if (!filter_var($duracao_dias, FILTER_VALIDATE_INT) || $duracao_dias < 1)   $erros['duracao_dias'] = 'Duracao deve ser um numero inteiro positivo.';
    if (!is_numeric($preco) || $preco < 0)                                      $erros['preco']      = 'Preco deve ser um valor numerico positivo.';
    if (filter_var($vagas_disponiveis, FILTER_VALIDATE_INT) === false || $vagas_disponiveis < 0)
                                                                                $erros['vagas_disponiveis'] = 'Vagas deve ser um numero inteiro nao negativo.';

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE pacotes SET nome = :nome, destino_id = :destino_id, duracao_dias = :duracao_dias,
                 preco = :preco, vagas_disponiveis = :vagas_disponiveis WHERE id = :id'
            );
            $stmt->execute([
                ':nome'              => $nome,
                ':destino_id'        => (int) $destino_id,
                ':duracao_dias'      => (int) $duracao_dias,
                ':preco'             => (float) $preco,
                ':vagas_disponiveis' => (int) $vagas_disponiveis,
                ':id'                => $id,
            ]);

            $_SESSION['msg']      = 'Pacote atualizado com sucesso!';
            $_SESSION['msg_tipo'] = 'success';
            header('Location: index.php?page=pacotes');
            exit;
        } catch (Exception $e) {
            $erros['db'] = 'Erro ao atualizar o pacote. Tente novamente.';
        }
    }
}
?>

<div class="container">
    <h2>Editar Pacote</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">Corrija os erros abaixo para continuar.</div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="index.php?page=pacotes_editar&id=<?= $id ?>">
            <?= csrfInput() ?>
            <div class="form-group <?= isset($erros['nome']) ? 'has-error' : '' ?>">
                <label for="nome">Nome <span class="required">*</span></label>
                <input type="text" id="nome" name="nome" maxlength="200" value="<?= htmlspecialchars($nome) ?>">
                <?php if (isset($erros['nome'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['nome']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['destino_id']) ? 'has-error' : '' ?>">
                <label for="destino_id">Destino <span class="required">*</span></label>
                <select id="destino_id" name="destino_id">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($destinos as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $destino_id == $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erros['destino_id'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['destino_id']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['duracao_dias']) ? 'has-error' : '' ?>">
                <label for="duracao_dias">Duracao (dias) <span class="required">*</span></label>
                <input type="number" id="duracao_dias" name="duracao_dias" min="1" value="<?= htmlspecialchars($duracao_dias) ?>">
                <?php if (isset($erros['duracao_dias'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['duracao_dias']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['preco']) ? 'has-error' : '' ?>">
                <label for="preco">Preco (R$) <span class="required">*</span></label>
                <input type="text" id="preco" name="preco" value="<?= htmlspecialchars($preco) ?>">
                <?php if (isset($erros['preco'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['preco']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['vagas_disponiveis']) ? 'has-error' : '' ?>">
                <label for="vagas_disponiveis">Vagas Disponiveis <span class="required">*</span></label>
                <input type="number" id="vagas_disponiveis" name="vagas_disponiveis" min="0" value="<?= htmlspecialchars($vagas_disponiveis) ?>">
                <?php if (isset($erros['vagas_disponiveis'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['vagas_disponiveis']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="index.php?page=pacotes" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
