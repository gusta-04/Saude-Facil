<?php
$pageTitle = 'Painel';
require '../includes/conexao.php';
require '../includes/auth.php';
requerLogin();

$pdo  = conectar();
$user = usuarioLogado();

// Cancelar consulta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'])) {
    $cid = (int)$_POST['cancelar_id'];
    $upd = $pdo->prepare(
        "UPDATE consultas SET status='cancelada' WHERE id=? AND paciente_id=? AND status='agendada'"
    );
    $upd->execute([$cid, $user['id']]);
}

// Buscar dados do usuário
$dadosUser = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
$dadosUser->execute([$user['id']]);
$dadosUser = $dadosUser->fetch();

// Consultas
$consultas = $pdo->prepare(
    "SELECT c.*, a.data, a.hora, m.nome AS medico,
            e.nome AS especialidade, u.nome AS unidade
     FROM consultas c
     JOIN agenda a ON a.id = c.agenda_id
     JOIN medicos m ON m.id = a.medico_id
     JOIN especialidades e ON e.id = m.especialidade_id
     JOIN unidades u ON u.id = m.unidade_id
     WHERE c.paciente_id = ?
     ORDER BY a.data DESC, a.hora DESC"
);
$consultas->execute([$user['id']]);
$consultas = $consultas->fetchAll();

$total     = count($consultas);
$agendadas = count(array_filter($consultas, fn($c) => $c['status'] === 'agendada'));
$realizadas = count(array_filter($consultas, fn($c) => $c['status'] === 'realizada'));

require '../includes/header.php';
?>

<section class="sf-section">
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="sf-section-title fw-bold mb-1">Olá, <?= htmlspecialchars(explode(' ', $dadosUser['nome'])[0]) ?>!</h2>
            <p class="sf-section-sub mb-0">Gerencie suas consultas médicas</p>
        </div>
        <a href="agendar.php" class="btn btn-sf-primary">
            <i class="bi bi-calendar2-plus me-2"></i>Nova Consulta
        </a>
    </div>

    <!-- Resumo -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="sf-card card p-3 text-center">
                <div class="stat-num text-sf-primary" style="font-size:2rem;font-weight:800;"><?= $total ?></div>
                <div class="text-muted small">Total de Consultas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sf-card card p-3 text-center">
                <div class="stat-num" style="font-size:2rem;font-weight:800;color:var(--sf-primary);"><?= $agendadas ?></div>
                <div class="text-muted small">Agendadas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="sf-card card p-3 text-center">
                <div class="stat-num" style="font-size:2rem;font-weight:800;color:#2471a3;"><?= $realizadas ?></div>
                <div class="text-muted small">Realizadas</div>
            </div>
        </div>
    </div>

    <!-- Lista de consultas -->
    <div class="sf-card card p-4">
        <h5 class="fw-bold mb-3 text-sf-primary"><i class="bi bi-journal-medical me-2"></i>Minhas Consultas</h5>
        <?php if (empty($consultas)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
            <p class="mt-3 text-muted">Você ainda não tem consultas agendadas.</p>
            <a href="agendar.php" class="btn btn-sf-primary mt-2">Agendar Agora</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table sf-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Data</th><th>Hora</th><th>Especialidade</th>
                        <th>Médico</th><th>Unidade</th><th>Status</th><th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $c): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
                        <td><?= substr($c['hora'],0,5) ?></td>
                        <td><?= htmlspecialchars($c['especialidade']) ?></td>
                        <td><?= htmlspecialchars($c['medico']) ?></td>
                        <td><?= htmlspecialchars($c['unidade']) ?></td>
                        <td>
                            <span class="badge badge-<?= $c['status'] ?> px-3 py-2">
                                <?= ucfirst($c['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($c['status'] === 'agendada' && $c['data'] >= date('Y-m-d')): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="cancelar_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-cancelar">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</section>

<?php require '../includes/footer.php'; ?>
