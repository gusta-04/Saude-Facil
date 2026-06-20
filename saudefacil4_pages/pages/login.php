<?php
$pageTitle = 'Login';
require '../includes/conexao.php';
require '../includes/auth.php';

if (estaLogado()) { header('Location: painel.php'); exit; }

$erro = '';
$msg  = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nome']       = $user['nome'];
            $_SESSION['perfil']     = $user['perfil'];
            header('Location: painel.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}

require '../includes/header.php';
?>

<section class="sf-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <h2 class="sf-section-title fw-bold">Entrar</h2>
                    <p class="sf-section-sub">Acesse sua conta SaúdeFácil</p>
                </div>

                <?php if ($msg === 'login_necessario'): ?>
                <div class="alert alert-warning sf-flash alert-dismissible" role="alert">
                    <i class="bi bi-lock me-2"></i>Faça login para continuar.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if ($erro): ?>
                <div class="alert alert-danger sf-flash alert-dismissible" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="sf-form-card card">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">E-mail</label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-sf-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                        </button>
                    </form>
                    <hr>
                    <p class="text-center mb-0 small text-muted">
                        Não tem conta? <a href="cadastro.php" class="text-sf-primary fw-bold">Cadastre-se grátis</a>
                    </p>
                    <p class="text-center mt-2 small text-muted">
                        <em>Admin teste:</em> admin@saudefacil.com / <em>password</em>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require '../includes/footer.php'; ?>
