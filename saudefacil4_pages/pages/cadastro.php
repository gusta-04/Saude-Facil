<?php
$pageTitle = 'Cadastro';
require '../includes/conexao.php';
require '../includes/auth.php';

if (estaLogado()) { header('Location: painel.php'); exit; }

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $cpf      = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $senha    = $_POST['senha']         ?? '';
    $confirma = $_POST['confirma']      ?? '';
    $telefone = trim($_POST['telefone'] ?? '');
    $dataNasc = $_POST['data_nasc']     ?? '';

    if (!$nome || !$cpf || !$email || !$senha) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (strlen($cpf) !== 11) {
        $erro = 'CPF inválido.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        $pdo = conectar();
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE cpf=? OR email=?");
        $check->execute([$cpf, $email]);
        if ($check->fetch()) {
            $erro = 'CPF ou e-mail já cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $ins = $pdo->prepare(
                "INSERT INTO usuarios (nome,cpf,email,senha,telefone,data_nasc) VALUES (?,?,?,?,?,?)"
            );
            $ins->execute([$nome, $cpf, $email, $hash, $telefone ?: null, $dataNasc ?: null]);
            $sucesso = 'Cadastro realizado com sucesso! Faça login para continuar.';
        }
    }
}

require '../includes/header.php';
?>

<section class="sf-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="text-center mb-4">
                    <h2 class="sf-section-title fw-bold">Criar Conta</h2>
                    <p class="sf-section-sub">Cadastre-se gratuitamente e acesse o sistema</p>
                </div>

                <?php if ($erro): ?>
                <div class="alert alert-danger alert-dismissible sf-flash" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                <div class="alert alert-success alert-dismissible sf-flash" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="sf-form-card card">
                    <form method="POST" action="cadastro.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome completo *</label>
                            <input type="text" name="nome" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">CPF *</label>
                                <input type="text" name="cpf" class="form-control" required
                                       placeholder="000.000.000-00" maxlength="14"
                                       value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" name="telefone" class="form-control" placeholder="(54) 9xxxx-xxxx"
                                       value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">E-mail *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Data de Nascimento</label>
                            <input type="date" name="data_nasc" class="form-control"
                                   value="<?= htmlspecialchars($_POST['data_nasc'] ?? '') ?>">
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Senha *</label>
                                <input type="password" name="senha" class="form-control" required minlength="6">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Confirmar Senha *</label>
                                <input type="password" name="confirma" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sf-primary w-100 py-2">
                            <i class="bi bi-person-plus me-2"></i>Criar Minha Conta
                        </button>
                    </form>
                    <hr>
                    <p class="text-center mb-0 small text-muted">
                        Já tem conta? <a href="login.php" class="text-sf-primary fw-bold">Faça login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require '../includes/footer.php'; ?>
