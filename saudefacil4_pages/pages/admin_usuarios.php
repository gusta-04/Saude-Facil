<?php
$pageTitle = 'Gerenciar Usuários';
require '../includes/conexao.php';
require '../includes/auth.php';
requerAdmin();

$pdo = conectar();
$sucesso = '';
$erro = '';

// Excluir usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $id = (int)$_POST['excluir_id'];
    if ($id === (int)$_SESSION['usuario_id']) {
        $erro = 'Você não pode excluir o próprio usuário.';
    } else {
        $pdo->prepare("DELETE FROM consultas WHERE paciente_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
        $sucesso = 'Usuário excluído com sucesso.';
    }
}

// Alterar perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar_perfil_id'])) {
    $id     = (int)$_POST['alterar_perfil_id'];
    $perfil = in_array($_POST['novo_perfil'], ['paciente','admin']) ? $_POST['novo_perfil'] : 'paciente';
    $pdo->prepare("UPDATE usuarios SET perfil=? WHERE id=?")->execute([$perfil, $id]);
    $sucesso = 'Perfil atualizado.';
}

// Busca
$busca = trim($_GET['busca'] ?? '');
$sql   = "SELECT * FROM usuarios WHERE 1=1";
$params = [];
if ($busca) {
    $sql .= " AND (nome LIKE ? OR email LIKE ? OR cpf LIKE ?)";
    $like = "%$busca%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY criado_em DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

require '../includes/header.php';
?>

<section class="sf-section">
<div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <a href="admin.php" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Admin
            </a>
            <h2 class="sf-section-title fw-bold mb-0 mt-1">Gerenciar Usuários</h2>
        </div>
        <a href="cadastro.php" class="btn btn-sf-primary">
            <i class="bi bi-person-plus me-2"></i>Novo Usuário
        </a>
    </div>

    <?php if ($sucesso): ?>
    <div class="alert alert-success sf-flash alert-dismissible" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if ($erro): ?>
    <div class="alert alert-danger sf-flash alert-dismissible" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Busca -->
    <form method="GET" class="sf-form-card card mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Buscar usuário</label>
                <input type="text" name="busca" class="form-control"
                       placeholder="Nome, e-mail ou CPF..."
                       value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sf-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
            <div class="col-md-2">
                <a href="admin_usuarios.php" class="btn btn-outline-secondary w-100">Limpar</a>
            </div>
        </div>
    </form>

    <!-- Tabela -->
    <div class="sf-card card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-sf-primary mb-0">
                <i class="bi bi-people me-2"></i><?= count($usuarios) ?> usuário(s) encontrado(s)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table sf-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Perfil</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td class="text-muted small"><?= $u['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="small"><?= substr($u['cpf'],0,3).'.***.***-'.substr($u['cpf'],-2) ?></td>
                        <td class="small"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="small"><?= htmlspecialchars($u['telefone'] ?? '—') ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="alterar_perfil_id" value="<?= $u['id'] ?>">
                                <select name="novo_perfil" class="form-select form-select-sm"
                                        style="width:110px;display:inline-block;"
                                        onchange="this.form.submit()">
                                    <option value="paciente" <?= $u['perfil']==='paciente'?'selected':'' ?>>Paciente</option>
                                    <option value="admin"    <?= $u['perfil']==='admin'   ?'selected':'' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td class="small text-muted"><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
                        <td>
                            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="excluir_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-cancelar"
                                        title="Excluir usuário">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="badge bg-secondary">Você</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($usuarios)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Nenhum usuário encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

<?php require '../includes/footer.php'; ?>
