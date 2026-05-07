<?php
$pdo = getConnection();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['msg']      = 'Reserva invalida.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=reservas');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM reservas WHERE id = :id');
$stmt->execute([':id' => $id]);
$reserva = $stmt->fetch();

if (!$reserva) {
    $_SESSION['msg']      = 'Reserva nao encontrada.';
    $_SESSION['msg_tipo'] = 'danger';
    header('Location: index.php?page=reservas');
    exit;
}

$clientes = $pdo->query('SELECT id, nome FROM clientes ORDER BY nome')->fetchAll();
$pacotes  = $pdo->query('SELECT id, nome, preco FROM pacotes ORDER BY nome')->fetchAll();

$statusOpcoes = ['pendente' => 'Pendente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada'];

$erros = [];
$statusAnterior = $reserva['status'];
$pacoteAnterior = $reserva['pacote_id'];
$cliente_id     = $reserva['cliente_id'];
$pacote_id      = $reserva['pacote_id'];
$data_reserva   = $reserva['data_reserva'];
$status         = $reserva['status'];
$valor_pago     = $reserva['valor_pago'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarCsrfToken($_POST['csrf_token'] ?? '')) {
        $erros['csrf'] = 'Token de seguranca invalido. Reenvie o formulario.';
    }

    $cliente_id   = $_POST['cliente_id'] ?? '';
    $pacote_id    = $_POST['pacote_id'] ?? '';
    $data_reserva = trim($_POST['data_reserva'] ?? '');
    $status       = $_POST['status'] ?? '';
    $valor_pago   = $_POST['valor_pago'] ?? '';

    if (!filter_var($cliente_id, FILTER_VALIDATE_INT))          $erros['cliente_id']   = 'Selecione um cliente valido.';
    if (!filter_var($pacote_id, FILTER_VALIDATE_INT))           $erros['pacote_id']    = 'Selecione um pacote valido.';
    if ($data_reserva === '' || !strtotime($data_reserva))      $erros['data_reserva'] = 'Data da reserva invalida.';
    if (!array_key_exists($status, $statusOpcoes))              $erros['status']       = 'Status invalido.';
    if (!is_numeric($valor_pago) || $valor_pago < 0)            $erros['valor_pago']   = 'Valor pago deve ser um numero nao negativo.';

    if (empty($erros)) {
        // Determina se precisa devolver vaga (cancelando reserva ativa)
        $devolverVaga = ($status === 'cancelada' && $statusAnterior !== 'cancelada');
        // Determina se precisa consumir vaga (reativando reserva cancelada)
        $consumirVaga = ($status !== 'cancelada' && $statusAnterior === 'cancelada');

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'UPDATE reservas SET cliente_id = :cliente_id, pacote_id = :pacote_id,
                 data_reserva = :data_reserva, status = :status, valor_pago = :valor_pago
                 WHERE id = :id'
            );
            $stmt->execute([
                ':cliente_id'   => (int) $cliente_id,
                ':pacote_id'    => (int) $pacote_id,
                ':data_reserva' => $data_reserva,
                ':status'       => $status,
                ':valor_pago'   => (float) $valor_pago,
                ':id'           => $id,
            ]);

            if ($devolverVaga) {
                $stmt = $pdo->prepare('UPDATE pacotes SET vagas_disponiveis = vagas_disponiveis + 1 WHERE id = :id');
                $stmt->execute([':id' => (int) $pacoteAnterior]);
            }

            if ($consumirVaga) {
                // Verifica vagas antes de consumir
                $stmt = $pdo->prepare('SELECT vagas_disponiveis FROM pacotes WHERE id = :id FOR UPDATE');
                $stmt->execute([':id' => (int) $pacote_id]);
                $vagas = (int) $stmt->fetchColumn();

                if ($vagas <= 0) {
                    $pdo->rollBack();
                    $erros['status'] = 'Nao e possivel reativar: o pacote nao possui vagas disponiveis.';
                } else {
                    $stmt = $pdo->prepare('UPDATE pacotes SET vagas_disponiveis = vagas_disponiveis - 1 WHERE id = :id');
                    $stmt->execute([':id' => (int) $pacote_id]);
                    $pdo->commit();

                    $_SESSION['msg']      = 'Reserva atualizada com sucesso!';
                    $_SESSION['msg_tipo'] = 'success';
                    header('Location: index.php?page=reservas');
                    exit;
                }
            } else {
                $pdo->commit();

                $_SESSION['msg']      = 'Reserva atualizada com sucesso!';
                $_SESSION['msg_tipo'] = 'success';
                header('Location: index.php?page=reservas');
                exit;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $erros['status'] = 'Erro ao processar a atualizacao. Tente novamente.';
        }
    }
}

$precosJson = json_encode(array_column($pacotes, 'preco', 'id'));
?>

<div class="container">
    <h2>Editar Reserva</h2>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger">Corrija os erros abaixo para continuar.</div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="index.php?page=reservas_editar&id=<?= $id ?>">
            <?= csrfInput() ?>
            <div class="form-group <?= isset($erros['cliente_id']) ? 'has-error' : '' ?>">
                <label for="cliente_id">Cliente <span class="required">*</span></label>
                <select id="cliente_id" name="cliente_id">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $cliente_id == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erros['cliente_id'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['cliente_id']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['pacote_id']) ? 'has-error' : '' ?>">
                <label for="pacote_id">Pacote <span class="required">*</span></label>
                <select id="pacote_id" name="pacote_id">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($pacotes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $pacote_id == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nome']) ?> — R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erros['pacote_id'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['pacote_id']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['data_reserva']) ? 'has-error' : '' ?>">
                <label for="data_reserva">Data da Reserva <span class="required">*</span></label>
                <input type="date" id="data_reserva" name="data_reserva" value="<?= htmlspecialchars($data_reserva) ?>">
                <?php if (isset($erros['data_reserva'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['data_reserva']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['status']) ? 'has-error' : '' ?>">
                <label for="status">Status <span class="required">*</span></label>
                <select id="status" name="status">
                    <?php foreach ($statusOpcoes as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($erros['status'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['status']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?= isset($erros['valor_pago']) ? 'has-error' : '' ?>">
                <label for="valor_pago">Valor Pago (R$) <span class="required">*</span></label>
                <input type="text" id="valor_pago" name="valor_pago" value="<?= htmlspecialchars($valor_pago) ?>">
                <span class="field-hint">Preenchido automaticamente ao selecionar o pacote. Pode alterar manualmente.</span>
                <?php if (isset($erros['valor_pago'])): ?>
                    <span class="field-error"><?= htmlspecialchars($erros['valor_pago']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="index.php?page=reservas" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var precos = <?= $precosJson ?>;
    var selectPacote = document.getElementById('pacote_id');
    var inputValor   = document.getElementById('valor_pago');

    selectPacote.addEventListener('change', function() {
        var id = this.value;
        if (precos[id] !== undefined) {
            inputValor.value = precos[id];
        }
    });
})();
</script>
