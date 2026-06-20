<?php
// includes/conexao.php
// Configuração de conexão com MySQL (XAMPP)

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // padrão XAMPP: sem senha
define('DB_NAME', 'saudefacil');

function conectar(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div class="alert alert-danger m-4"><strong>Erro de conexão:</strong> ' .
                htmlspecialchars($e->getMessage()) . '</div>');
        }
    }
    return $pdo;
}
