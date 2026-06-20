<?php
$pageTitle = 'Gerenciar Unidades';
require '../includes/conexao.php';
require '../includes/auth.php';
requerAdmin();

$pdo     = conectar();
$sucesso = '';
$erro    = '';

// ---------- EXCLUIR ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $id = (int)$_POST['excluir_id'];

    // Verifica se há médicos vinculados
    $medicos = $pdo->prepare("SELECT COUNT(*) FROM medicos WHERE unidade_id = ?");
    $medicos->execute([$id]);
    if ($medicos->fetchColumn() > 0) {
        $erro = 'Não é possível excluir: há médicos vinculados a esta unidade. Remova os médicos primeiro.';
    } else {
        $pdo->prepare("DELETE FROM unidades WHERE id = ?")->execute([$id]);
        $sucesso = 'Unidade excluída com sucesso.';
    }
}

// ---------- SALVAR (novo ou editar) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $uid      = (int)($_POST['unidade_id'] ?? 0);
    $nome     = trim($_POST['nome']      ?? '');
    $endereco = trim($_POST['endereco']  ?? '');
    $bairro   = trim($_POST['bairro']    ?? '');
    $cidade   = trim($_POST['cidade']    ?? 'Passo Fundo');
    $telefone = trim($_POST['telefone']  ?? '');
    $ativo    = isset($_POST['ativo']) ? 1 : 0;

    if (!$nome || !$endereco || !$bairro) {
        $erro = 'Preencha os campos obrigatórios: Nome, Endereço e Bairro.';
    } else {
        if ($uid) {
            $pdo->prepare(
                "UPDATE unidades SET nome=?, endereco=?, bairro=?, cidade=?, telefone=?, ativo=? WHERE id=?"
            )->execute([$nome, $endereco, $bairro, $cidade, $telefone ?: null, $ativo, $uid]);
            $sucesso = 'Unidade atualizada com sucesso.';
        } else {
            $pdo->prepare(
                "INSERT INTO unidades (nome, endereco, bairro, cidade, telefone, ativo) VALUES (?,?,?,?,?,?)"
            )->execute([$nome, $endereco, $bairro, $cidade, $telefone ?: null, $ativo]);
            $sucesso = 'Unidade cadastrada com sucesso.';
        }
    }
}

// ---------- BUSCAR UNIDADE PARA EDIÇÃO ----------
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM unidades WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

// ---------- LISTAR UNIDADES ----------
$unidades = $pdo->query(
    "SELECT u.*, COUNT(m.id) AS total_medicos
     FROM unidades u
     LEFT JOIN medicos m ON m.unidade_id = u.id
     GROUP BY u.id
     ORDER BY u.nome"
)->fetchAll();

require '../includes/header.php';
?>

<section class="sf-section">
<div class="container">

    <!-- Cabeçalho -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <a href="admin.php" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Admin
            </a>
            <h2 class="sf-section-title fw-bold mb-0 mt-1">
                <i class="bi bi-building-fill-cross me-2"></i>Gerenciar Unidades de Saúde
            </h2>
        </div>
        <a href="admin_unidades.php" class="btn btn-sf-primary">
            <i class="bi bi-plus-circle me-2"></i>Nova Unidade
        </a>
    </div>

    <!-- Alertas -->
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

    <div class="row g-4">

        <!-- ===== FORMULÁRIO ===== -->
        <div class="col-lg-4">
            <div class="sf-form-card card">
                <h5 class="fw-bold mb-4 text-sf-primary">
                    <i class="bi bi-<?= $editando ? 'pencil-square' : 'plus-circle' ?> me-2"></i>
                    <?= $editando ? 'Editar Unidade' : 'Nova Unidade' ?>
                </h5>

                <form method="POST" action="admin_unidades.php">
                    <input type="hidden" name="salvar" value="1">
                    <input type="hidden" name="unidade_id" value="<?= $editando['id'] ?? 0 ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nome da Unidade <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nome" class="form-control" required
                               placeholder="Ex: UBS Centro"
                               value="<?= htmlspecialchars($editando['nome'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Endereço <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="endereco" class="form-control" required
                               placeholder="Ex: Rua Moron, 1400"
                               value="<?= htmlspecialchars($editando['endereco'] ?? '') ?>">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">
                                Bairro <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="bairro" class="form-control" required
                                   placeholder="Ex: Centro"
                                   value="<?= htmlspecialchars($editando['bairro'] ?? '') ?>">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Cidade</label>
                            <input type="text" name="cidade" class="form-control"
                                   placeholder="Passo Fundo"
                                   value="<?= htmlspecialchars($editando['cidade'] ?? 'Passo Fundo') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Telefone</label>
                        <input type="text" name="telefone" class="form-control"
                               placeholder="(54) 3000-0000"
                               value="<?= htmlspecialchars($editando['telefone'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo"
                                   id="chkAtivo" role="switch"
                                   <?= ($editando['ativo'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="chkAtivo">
                                Unidade ativa
                            </label>
                        </div>
                        <div class="form-text text-muted">
                            Unidades inativas não aparecem para agendamento.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sf-primary w-100 py-2">
                        <i class="bi bi-floppy me-2"></i>
                        <?= $editando ? 'Salvar Alterações' : 'Cadastrar Unidade' ?>
                    </button>

                    <?php if ($editando): ?>
                    <a href="admin_unidades.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-x me-1"></i>Cancelar edição
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Legenda -->
            <div class="sf-card card p-3 mt-3 no-hover">
                <p class="small text-muted mb-2 fw-bold"><i class="bi bi-info-circle me-1"></i>Informações</p>
                <p class="small text-muted mb-1">
                    <span class="badge badge-agendada me-1">Ativa</span> Aparece no agendamento
                </p>
                <p class="small text-muted mb-0">
                    <span class="badge badge-cancelada me-1">Inativa</span> Oculta para pacientes
                </p>
            </div>
        </div>

        <!-- ===== LISTA DE UNIDADES ===== -->
        <div class="col-lg-8">

            <!-- Cards das unidades -->
            <div class="row g-3 mb-4">
                <?php foreach ($unidades as $u): ?>
                <div class="col-md-6">
                    <div class="sf-card card p-0 overflow-hidden h-100
                                <?= $editando && $editando['id'] == $u['id'] ? 'border border-2 border-warning' : '' ?>">

                        <!-- Cabeçalho do card -->
                        <div class="p-3 d-flex align-items-center justify-content-between"
                             style="background: <?= $u['ativo'] ? 'var(--sf-light-bg)' : '#f8f8f8' ?>;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="card-icon mb-0" style="width:40px;height:40px;font-size:1.1rem;
                                     background:<?= $u['ativo'] ? 'var(--sf-primary)' : '#ccc' ?>;color:#fff;">
                                    <i class="bi bi-building-fill-cross"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small"><?= htmlspecialchars($u['nome']) ?></div>
                                    <span class="badge <?= $u['ativo'] ? 'badge-agendada' : 'badge-cancelada' ?> px-2">
                                        <?= $u['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </div>
                            </div>
                            <span class="badge bg-sf-primary text-white px-2 py-1">
                                <i class="bi bi-person-badge me-1"></i><?= $u['total_medicos'] ?> médico(s)
                            </span>
                        </div>

                        <!-- Corpo do card -->
                        <div class="p-3">
                            <p class="small text-muted mb-1">
                                <i class="bi bi-geo-alt text-sf-primary me-1"></i>
                                <?= htmlspecialchars($u['endereco']) ?>
                            </p>
                            <p class="small text-muted mb-1">
                                <i class="bi bi-map text-sf-primary me-1"></i>
                                <?= htmlspecialchars($u['bairro']) ?> – <?= htmlspecialchars($u['cidade']) ?>
                            </p>
                            <?php if ($u['telefone']): ?>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-telephone text-sf-primary me-1"></i>
                                <?= htmlspecialchars($u['telefone']) ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Ações -->
                        <div class="p-3 pt-0 d-flex gap-2">
                            <a href="admin_unidades.php?editar=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <form method="POST" style="flex:1;">
                                <input type="hidden" name="excluir_id" value="<?= $u['id'] ?>">
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger w-100 btn-cancelar"
                                        <?= $u['total_medicos'] > 0 ? 'title="Possui médicos vinculados"' : '' ?>>
                                    <i class="bi bi-trash me-1"></i>Excluir
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($unidades)): ?>
                <div class="col-12">
                    <div class="sf-empty-state">
                        <i class="bi bi-building-x"></i>
                        <p>Nenhuma unidade cadastrada ainda.</p>
                        <p class="small">Use o formulário ao lado para adicionar a primeira unidade.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tabela resumo -->
            <div class="sf-card card p-4 no-hover">
                <h6 class="fw-bold text-sf-primary mb-3">
                    <i class="bi bi-table me-2"></i>Resumo – <?= count($unidades) ?> unidade(s) cadastrada(s)
                </h6>
                <div class="table-responsive">
                    <table class="table sf-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Bairro</th>
                                <th>Telefone</th>
                                <th>Médicos</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unidades as $u): ?>
                            <tr>
                                <td class="text-muted small"><?= $u['id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($u['nome']) ?></td>
                                <td class="small"><?= htmlspecialchars($u['bairro']) ?></td>
                                <td class="small"><?= htmlspecialchars($u['telefone'] ?? '—') ?></td>
                                <td>
                                    <span class="badge bg-sf-primary text-white">
                                        <?= $u['total_medicos'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $u['ativo'] ? 'badge-agendada' : 'badge-cancelada' ?>">
                                        <?= $u['ativo'] ? 'Ativa' : 'Inativa' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="admin_unidades.php?editar=<?= $u['id'] ?>"
                                       class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="excluir_id" value="<?= $u['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger btn-cancelar"
                                                title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
</section>

<?php require '../includes/footer.php'; ?>
