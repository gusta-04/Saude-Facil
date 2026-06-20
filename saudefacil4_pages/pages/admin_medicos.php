<?php
$pageTitle = 'Gerenciar Médicos';
require '../includes/conexao.php';
require '../includes/auth.php';
requerAdmin();

$pdo = conectar();
$sucesso = '';
$erro = '';

// Excluir médico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $id = (int)$_POST['excluir_id'];
    // Remove agenda e consultas vinculadas
    $agendas = $pdo->prepare("SELECT id FROM agenda WHERE medico_id=?");
    $agendas->execute([$id]);
    foreach ($agendas->fetchAll() as $ag) {
        $pdo->prepare("DELETE FROM consultas WHERE agenda_id=?")->execute([$ag['id']]);
    }
    $pdo->prepare("DELETE FROM agenda WHERE medico_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM medicos WHERE id=?")->execute([$id]);
    $sucesso = 'Médico excluído com sucesso.';
}

// Cadastrar/Editar médico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $mid    = (int)($_POST['medico_id'] ?? 0);
    $nome   = trim($_POST['nome'] ?? '');
    $crm    = trim($_POST['crm']  ?? '');
    $espId  = (int)$_POST['especialidade_id'];
    $unidId = (int)$_POST['unidade_id'];

    if (!$nome || !$crm || !$espId || !$unidId) {
        $erro = 'Preencha todos os campos.';
    } else {
        if ($mid) {
            $pdo->prepare("UPDATE medicos SET nome=?,crm=?,especialidade_id=?,unidade_id=? WHERE id=?")
                ->execute([$nome,$crm,$espId,$unidId,$mid]);
            $sucesso = 'Médico atualizado.';
        } else {
            try {
                $pdo->prepare("INSERT INTO medicos (nome,crm,especialidade_id,unidade_id) VALUES (?,?,?,?)")
                    ->execute([$nome,$crm,$espId,$unidId]);
                $sucesso = 'Médico cadastrado com sucesso.';
            } catch (PDOException $e) {
                $erro = 'CRM já cadastrado.';
            }
        }
    }
}

$medicos = $pdo->query(
    "SELECT m.*, e.nome AS especialidade, u.nome AS unidade
     FROM medicos m
     JOIN especialidades e ON e.id = m.especialidade_id
     JOIN unidades u ON u.id = m.unidade_id
     ORDER BY m.nome"
)->fetchAll();

$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome")->fetchAll();
$unidades       = $pdo->query("SELECT * FROM unidades WHERE ativo=1 ORDER BY nome")->fetchAll();

// Buscar médico para edição
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM medicos WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

require '../includes/header.php';
?>

<section class="sf-section">
<div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <a href="admin.php" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Admin
            </a>
            <h2 class="sf-section-title fw-bold mb-0 mt-1">Gerenciar Médicos</h2>
        </div>
    </div>

    <?php if ($sucesso): ?>
    <div class="alert alert-success sf-flash alert-dismissible">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if ($erro): ?>
    <div class="alert alert-danger sf-flash alert-dismissible">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Formulário -->
        <div class="col-lg-4">
            <div class="sf-form-card card">
                <h5 class="fw-bold mb-4 text-sf-primary">
                    <i class="bi bi-person-badge me-2"></i>
                    <?= $editando ? 'Editar Médico' : 'Novo Médico' ?>
                </h5>
                <form method="POST" action="admin_medicos.php">
                    <input type="hidden" name="salvar" value="1">
                    <input type="hidden" name="medico_id" value="<?= $editando['id'] ?? 0 ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome completo *</label>
                        <input type="text" name="nome" class="form-control" required
                               value="<?= htmlspecialchars($editando['nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">CRM *</label>
                        <input type="text" name="crm" class="form-control" required
                               placeholder="CRM/RS 00000"
                               value="<?= htmlspecialchars($editando['crm'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Especialidade *</label>
                        <select name="especialidade_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($especialidades as $e): ?>
                            <option value="<?= $e['id'] ?>"
                                <?= ($editando['especialidade_id'] ?? 0) == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Unidade de Saúde *</label>
                        <select name="unidade_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($unidades as $u): ?>
                            <option value="<?= $u['id'] ?>"
                                <?= ($editando['unidade_id'] ?? 0) == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sf-primary w-100">
                        <i class="bi bi-floppy me-2"></i>
                        <?= $editando ? 'Salvar Alterações' : 'Cadastrar Médico' ?>
                    </button>
                    <?php if ($editando): ?>
                    <a href="admin_medicos.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar edição</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-lg-8">
            <div class="sf-card card p-4">
                <h6 class="fw-bold text-sf-primary mb-3">
                    <i class="bi bi-list-ul me-2"></i><?= count($medicos) ?> médico(s) cadastrado(s)
                </h6>
                <div class="table-responsive">
                    <table class="table sf-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th><th>CRM</th><th>Especialidade</th><th>Unidade</th><th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicos as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($m['nome']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($m['crm']) ?></td>
                                <td><?= htmlspecialchars($m['especialidade']) ?></td>
                                <td class="small"><?= htmlspecialchars($m['unidade']) ?></td>
                                <td>
                                    <a href="admin_medicos.php?editar=<?= $m['id'] ?>"
                                       class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="excluir_id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-cancelar"
                                                title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($medicos)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Nenhum médico cadastrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</section>

<?php require '../includes/footer.php'; ?>
