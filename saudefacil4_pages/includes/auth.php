<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaLogado(): bool {
    return isset($_SESSION['usuario_id']);
}

function caminhoPagina(string $arquivo): string {
    return (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? $arquivo : 'pages/' . $arquivo;
}

function requerLogin(): void {
    if (!estaLogado()) {
        header('Location: ' . caminhoPagina('login.php?msg=login_necessario'));
        exit;
    }
}

function requerAdmin(): void {
    requerLogin();
    if (($_SESSION['perfil'] ?? '') !== 'admin') {
        header('Location: ' . caminhoPagina('painel.php?msg=sem_permissao'));
        exit;
    }
}

function usuarioLogado(): array {
    return [
        'id'     => $_SESSION['usuario_id'] ?? null,
        'nome'   => $_SESSION['nome']        ?? '',
        'perfil' => $_SESSION['perfil']      ?? 'paciente',
    ];
}

function logout(): void {
    session_destroy();
    header('Location: ../index.php');
    exit;
}
