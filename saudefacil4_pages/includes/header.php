<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
$logado      = isset($_SESSION['usuario_id']);
$nomeUsuario = $_SESSION['nome']   ?? '';
$perfil      = $_SESSION['perfil'] ?? '';
$basePath = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SaúdeFácil' ?> – SaúdeFácil</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- CSS SaúdeFácil (modular) -->
    <link href="<?= $basePath ?>css/base.css"    rel="stylesheet">
    <link href="<?= $basePath ?>css/navbar.css"  rel="stylesheet">
    <link href="<?= $basePath ?>css/buttons.css" rel="stylesheet">
    <link href="<?= $basePath ?>css/cards.css"   rel="stylesheet">
    <link href="<?= $basePath ?>css/forms.css"   rel="stylesheet">
    <link href="<?= $basePath ?>css/tables.css"  rel="stylesheet">
    <link href="<?= $basePath ?>css/pages.css"   rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sf-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $basePath ?>index.php">
            <span class="sf-logo-icon"><i class="bi bi-hospital-fill"></i></span>
            <span class="sf-logo-text">Saúde<strong>Fácil</strong></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>index.php">
                        <i class="bi bi-house-door me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>pages/unidades.php">
                        <i class="bi bi-hospital-fill me-1"> </i>Unidades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>pages/agendar.php">
                        <i class="bi bi-calendar2-plus me-1"></i>Agendar
                    </a>
                </li>
                <?php if ($logado): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>pages/painel.php">
                        <i class="bi bi-journal-medical me-1"></i>Minhas Consultas
                    </a>
                </li>
                <?php if ($perfil === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link sf-admin-link" href="<?= $basePath ?>pages/admin.php">
                        <i class="bi bi-gear-fill me-1"></i>Admin
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($logado): ?>
                    <span class="sf-user-badge">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nomeUsuario) ?>
                    </span>
                    <a href="<?= $basePath ?>pages/logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </a>
                <?php else: ?>
                    <a href="<?= $basePath ?>pages/login.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                    </a>
                    <a href="<?= $basePath ?>pages/cadastro.php" class="btn btn-sf-accent btn-sm">
                        <i class="bi bi-person-plus me-1"></i>Cadastrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
