<?php
$pdo = getConnection();

$totalDestinos = $pdo->query('SELECT COUNT(*) FROM destinos')->fetchColumn();
$totalPacotes  = $pdo->query('SELECT COUNT(*) FROM pacotes')->fetchColumn();
$totalClientes = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$totalReservas = $pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn();

$reservasPendentes  = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status = 'pendente'")->fetchColumn();
$reservasConfirmadas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status = 'confirmada'")->fetchColumn();

$totalVagas = $pdo->query('SELECT COALESCE(SUM(vagas_disponiveis), 0) FROM pacotes')->fetchColumn();
?>

<div class="container">
    <div class="dashboard-hero">
        <h1><?= htmlspecialchars(APP_NAME) ?></h1>
        <p class="dashboard-subtitle">Painel de controle — gerencie destinos, pacotes, clientes e reservas.</p>
    </div>

    <div class="dashboard-cards">
        <a href="index.php?page=destinos" class="card card--destinos">
            <div class="card-icon">&#9992;</div>
            <p class="card-number"><?= $totalDestinos ?></p>
            <h3>Destinos</h3>
            <p class="card-detail"><?= $totalDestinos == 1 ? '1 destino cadastrado' : $totalDestinos . ' destinos cadastrados' ?></p>
        </a>
        <a href="index.php?page=pacotes" class="card card--pacotes">
            <div class="card-icon">&#9871;</div>
            <p class="card-number"><?= $totalPacotes ?></p>
            <h3>Pacotes</h3>
            <p class="card-detail"><?= $totalVagas ?> <?= $totalVagas == 1 ? 'vaga disponivel' : 'vagas disponiveis' ?></p>
        </a>
        <a href="index.php?page=clientes" class="card card--clientes">
            <div class="card-icon">&#9823;</div>
            <p class="card-number"><?= $totalClientes ?></p>
            <h3>Clientes</h3>
            <p class="card-detail"><?= $totalClientes == 1 ? '1 cliente registrado' : $totalClientes . ' clientes registrados' ?></p>
        </a>
        <a href="index.php?page=reservas" class="card card--reservas">
            <div class="card-icon">&#9783;</div>
            <p class="card-number"><?= $totalReservas ?></p>
            <h3>Reservas</h3>
            <p class="card-detail"><?= $reservasPendentes ?> <?= $reservasPendentes == 1 ? 'pendente' : 'pendentes' ?> &middot; <?= $reservasConfirmadas ?> <?= $reservasConfirmadas == 1 ? 'confirmada' : 'confirmadas' ?></p>
        </a>
    </div>

    <div class="dashboard-quick">
        <h2>Acoes rapidas</h2>
        <div class="quick-links">
            <a href="index.php?page=destinos_criar" class="btn btn-primary">Novo Destino</a>
            <a href="index.php?page=pacotes_criar" class="btn btn-primary">Novo Pacote</a>
            <a href="index.php?page=clientes_criar" class="btn btn-primary">Novo Cliente</a>
            <a href="index.php?page=reservas_criar" class="btn btn-success">Nova Reserva</a>
        </div>
    </div>
</div>
