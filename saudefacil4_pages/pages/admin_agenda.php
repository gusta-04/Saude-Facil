<?php
$pageTitle = 'Gerenciar Agenda';
require '../includes/conexao.php';
require '../includes/auth.php';
requerAdmin();

$pdo = conectar();
$sucesso = '';
$erro = '';

// Excluir horário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $id = (int)$_POST['excluir_id'];
    $pdo->prepare("DELETE FROM consultas WHERE agenda_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM agenda WHERE id=?")->execute([$id]);
    $sucesso = 'Horário excluído.';
}

// Salvar horário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    $aid     = (int)($_POST['agenda_id'] ?? 0);
    $medId   = (int)$_POST['medico_id'];
    $data    = $_POST['data']  ?? '';
    $hora    = $_POST['hora']  ?? '';
    $vagas   = max(1, (int)($_POST['vagas'] ?? 1));

    if (!$medId || !$data || !$hora) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif ($data < date('Y-m-d')) {
        $erro = 'A data não pode ser no passado.';
    } else {
        if ($aid) {
            $pdo->prepare("UPDATE agenda SET medico_id=?,data=?,hora=?,vagas=? WHERE id=?")
                ->execute([$medId,$data,$hora,$vagas,$aid]);
            $sucesso = 'Horário atualizado.';
        } else {
            $pdo->prepare("INSERT INTO agenda (medico_id,data,hora,vagas) VALUES (?,?,?,?)")
                ->execute([$medId,$data,$hora,$vagas]);
            $sucesso = 'Horário cadastrado com sucesso.';
        }
    }
}

// Filtros
$filtroMed  = (int)($_GET['medico']  ?? 0);
$filtroData = $_GET['data'] ?? '';

$sqlAg = "SELECT a.*, m.nome AS medico, e.nome AS especialidade, u.nome AS unidade,
                 (SELECT COUNT(*) FROM consultas c WHERE c.agenda_id=a.id AND c.status='agendada') AS ocupadas
          FROM agenda a
          JOIN medicos m ON m.id = a.medico_id
          JOIN especialidades e ON e.id = m.especialidade_id
          JOIN unidades u ON u.id = m.unidade_id
          WHERE 1=1";
$params = [];
if ($filtroMed)  { $sqlAg .= " AND m.id=?";   $params[] = $filtroMed; }
if ($filtroData) { $sqlAg .= " AND a.data=?";  $params[] = $filtroData; }
$sqlAg .= " ORDER BY a.data, a.hora";

$stmt   = $pdo->prepare($sqlAg);
$stmt->execute($params);
$horarios = $stmt->fetchAll();

$medicos = $pdo->query(
    "SELECT m.*, e.nome AS especialidade FROM medicos m
     JOIN especialidades e ON e.id = m.especialidade_id ORDER BY m.nome"
)->fetchAll();

// Buscar agenda para edição
$editando = null;
if (isset($_GET['editar'])) {
    $stmt2 = $pdo->prepare("SELECT * FROM agenda WHERE id=?");
    $stmt2->execute([(int)$_GET['editar']]);
    $editando = $stmt2->fetch();
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
            <h2 class="sf-section-title fw-bold mb-0 mt-1">Gerenciar Agenda</h2>
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
                    <i class="bi bi-calendar2-plus me-2"></i>
                    <?= $editando ? 'Editar Horário' : 'Novo Horário' ?>
                </h5>
                <form method="POST" action="admin_agenda.php">
                    <input type="hidden" name="salvar" value="1">
                    <input type="hidden" name="agenda_id" value="<?= $editando['id'] ?? 0 ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Médico *</label>
                        <select name="medico_id" class="form-select" required>
                            <option value="">Selecione o médico...</option>
                            <?php foreach ($medicos as $m): ?>
                            <option value="<?= $m['id'] ?>"
                                <?= ($editando['medico_id'] ?? 0) == $m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nome']) ?> – <?= htmlspecialchars($m['especialidade']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Data *</label>
                        <input type="date" name="data" class="form-control" required
                               min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($editando['data'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Horário *</label>
                        <input type="time" name="hora" class="form-control" required
                               value="<?= htmlspecialchars(substr($editando['hora'] ?? '',0,5)) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Número de Vagas</label>
                        <input type="number" name="vagas" class="form-control"
                               min="1" max="20" value="<?= $editando['vagas'] ?? 1 ?>">
                    </div>
                    <button type="submit" class="btn btn-sf-primary w-100">
                        <i class="bi bi-floppy me-2"></i>
                        <?= $editando ? 'Salvar Alterações' : 'Adicionar Horário' ?>
                    </button>
                    <?php if ($editando): ?>
                    <a href="admin_agenda.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Filtros -->
            <div class="sf-form-card card mt-4">
                <h6 class="fw-bold mb-3 text-sf-primary"><i class="bi bi-funnel me-2"></i>Filtrar Agenda</h6>
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Médico</label>
                        <select name="medico" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <?php foreach ($medicos as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= $filtroMed==$m['id']?'selected':'' ?>>
                                <?= htmlspecialchars($m['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Data</label>
                        <input type="date" name="data" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($filtroData) ?>">
                    </div>
                    <button type="submit" class="btn btn-sf-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="admin_agenda.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Limpar</a>
                </form>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-lg-8">
            <div class="sf-card card p-4">
                <h6 class="fw-bold text-sf-primary mb-3">
                    <i class="bi bi-calendar3 me-2"></i><?= count($horarios) ?> horário(s) na agenda
                </h6>
                <div class="table-responsive">
                    <table class="table sf-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Médico</th>
                                <th>Especialidade</th>
                                <th>Unidade</th>
                                <th>Vagas</th>
                                <th>Ocupadas</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($horarios as $h): ?>
                            <?php $livre = $h['vagas'] - $h['ocupadas']; ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($h['data'])) ?></td>
                                <td><?= substr($h['hora'],0,5) ?></td>
                                <td class="small fw-semibold"><?= htmlspecialchars($h['medico']) ?></td>
                                <td class="small"><?= htmlspecialchars($h['especialidade']) ?></td>
                                <td class="small"><?= htmlspecialchars($h['unidade']) ?></td>
                                <td>
                                    <span class="badge bg-sf-primary text-white"><?= $h['vagas'] ?></span>
                                </td>
                                <td>
                                    <?php if ($livre <= 0): ?>
                                    <span class="badge bg-danger">Lotado</span>
                                    <?php elseif ($livre <= 1): ?>
                                    <span class="badge bg-warning text-dark"><?= $h['ocupadas'] ?>/<?= $h['vagas'] ?></span>
                                    <?php else: ?>
                                    <span class="badge badge-agendada"><?= $h['ocupadas'] ?>/<?= $h['vagas'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="admin_agenda.php?editar=<?= $h['id'] ?>"
                                       class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="excluir_id" value="<?= $h['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-cancelar"
                                                title="Excluir horário">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($horarios)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Nenhum horário encontrado.
                                </td>
                            </tr>
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
