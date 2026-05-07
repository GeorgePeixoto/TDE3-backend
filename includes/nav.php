<?php
$secaoAtiva = explode('_', $page)[0];

$secoes = [
    'destinos' => 'Destinos',
    'pacotes'  => 'Pacotes',
    'clientes' => 'Clientes',
    'reservas' => 'Reservas',
];
?>
<nav class="navbar">
    <a href="<?= BASE_URL ?>/index.php" class="navbar-brand"><?= htmlspecialchars(APP_NAME) ?></a>
    <ul class="navbar-links">
        <?php foreach ($secoes as $chave => $label): ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=<?= $chave ?>"
                   class="<?= $secaoAtiva === $chave ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
