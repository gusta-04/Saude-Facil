<?php
$pageTitle = 'Administração';
require '../includes/conexao.php';
require '../includes/auth.php';
requerAdmin();

$pdo = conectar();

// Estatísticas
$totalPacientes  = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil='paciente'")->fetchColumn();
$totalAgendadas  = $pdo->query("SELECT COUNT(*) FROM consultas WHERE status='agendada'")->fetchColumn();
$totalCanceladas = $pdo->query("SELECT COUNT(*) FROM consultas WHERE status='cancelada'")->fetchColumn();
$totalRealizadas = $pdo->query("SELECT COUNT(*) FROM consultas WHERE status='realizada'")->fetchColumn();
$totalUnidades   = $pdo->query("SELECT COUNT(*) FROM unidades WHERE ativo=1")->fetchColumn();
$totalMedicos    = $pdo->query("SELECT COUNT(*) FROM medicos")->fetchColumn();

// Consultas recentes
$recentes = $pdo->query(
    "SELECT c.id, c.status, c.criado_em, a.data, a.hora,
            u.nome AS paciente, m.nome AS medico,
            e.nome AS especialidade, un.nome AS unidade
     FROM consultas c
     JOIN usuarios u  ON u.id  = c.paciente_id
     JOIN agenda a    ON a.id  = c.agenda_id
     JOIN medicos m   ON m.id  = a.medico_id
     JOIN especialidades e ON e.id = m.especialidade_id
     JOIN unidades un ON un.id = m.unidade_id
     ORDER BY c.criado_em DESC
     LIMIT 20"
)->fetchAll();

require '../includes/header.php';
?>

<section class="sf-section">
<div class="container">

    <!-- Título -->
    <div class="d-flex align-items-center gap-3 mb-5">
        <div class="sf-admin-title-icon">
            <i class="bi bi-speedometer2"></i>
        </div>
        <div>
            <h2 class="sf-section-title fw-bold mb-0">Painel Administrativo</h2>
            <p class="text-muted small mb-0">Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?>!</p>
        </div>
    </div>

    <!-- ===== MÉTRICAS ===== -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#d1f0e0;">
                    <i class="bi bi-people-fill" style="color:var(--sf-primary);"></i>
                </div>
                <div class="sf-metric-num" style="color:var(--sf-primary);"><?= $totalPacientes ?></div>
                <div class="sf-metric-label">Pacientes</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#d1f0e0;">
                    <i class="bi bi-calendar-check" style="color:var(--sf-primary);"></i>
                </div>
                <div class="sf-metric-num" style="color:var(--sf-primary);"><?= $totalAgendadas ?></div>
                <div class="sf-metric-label">Agendadas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#e8f4fd;">
                    <i class="bi bi-check2-all" style="color:#2471a3;"></i>
                </div>
                <div class="sf-metric-num" style="color:#2471a3;"><?= $totalRealizadas ?></div>
                <div class="sf-metric-label">Realizadas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#fde8e8;">
                    <i class="bi bi-x-circle" style="color:#c0392b;"></i>
                </div>
                <div class="sf-metric-num" style="color:#c0392b;"><?= $totalCanceladas ?></div>
                <div class="sf-metric-label">Canceladas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#fff8e1;">
                    <i class="bi bi-hospital" style="color:#d99200;"></i>
                </div>
                <div class="sf-metric-num" style="color:#d99200;"><?= $totalUnidades ?></div>
                <div class="sf-metric-label">Unidades</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="sf-metric-card">
                <div class="sf-metric-icon" style="background:#f0e8fd;">
                    <i class="bi bi-person-badge" style="color:#7d3c98;"></i>
                </div>
                <div class="sf-metric-num" style="color:#7d3c98;"><?= $totalMedicos ?></div>
                <div class="sf-metric-label">Médicos</div>
            </div>
        </div>
    </div>

    <!-- ===== ATALHOS DE GESTÃO ===== -->
    <h5 class="fw-bold text-sf-primary mb-3">
        <i class="bi bi-grid-3x3-gap me-2"></i>Módulos de Gerenciamento
    </h5>
    <div class="row g-3 mb-5">

        <!-- Usuários -->
        <div class="col-6 col-md-3">
            <a href="admin_usuarios.php" class="text-decoration-none">
                <div class="sf-shortcut-card">
                    <div class="sf-shortcut-icon" style="background:#d1f0e0;">
                        <i class="bi bi-people-fill" style="color:var(--sf-primary);"></i>
                    </div>
                    <div class="sf-shortcut-label">Gerenciar Usuários</div>
                    <div class="sf-shortcut-desc">Pacientes e admins</div>
                </div>
            </a>
        </div>

        <!-- Médicos -->
        <div class="col-6 col-md-3">
            <a href="admin_medicos.php" class="text-decoration-none">
                <div class="sf-shortcut-card">
                    <div class="sf-shortcut-icon" style="background:#f0e8fd;">
                        <i class="bi bi-person-badge-fill" style="color:#7d3c98;"></i>
                    </div>
                    <div class="sf-shortcut-label">Gerenciar Médicos</div>
                    <div class="sf-shortcut-desc">CRM e especialidades</div>
                </div>
            </a>
        </div>

        <!-- Agenda -->
        <div class="col-6 col-md-3">
            <a href="admin_agenda.php" class="text-decoration-none">
                <div class="sf-shortcut-card">
                    <div class="sf-shortcut-icon" style="background:#e8f4fd;">
                        <i class="bi bi-calendar2-plus-fill" style="color:#2471a3;"></i>
                    </div>
                    <div class="sf-shortcut-label">Gerenciar Agenda</div>
                    <div class="sf-shortcut-desc">Horários e vagas</div>
                </div>
            </a>
        </div>

        <!-- Unidades -->
        <div class="col-6 col-md-3">
            <a href="admin_unidades.php" class="text-decoration-none">
                <div class="sf-shortcut-card">
                    <div class="sf-shortcut-icon" style="background:#fff8e1;">
                        <i class="bi bi-hospital-fill" style="color:#d99200;"></i>
                    </div>
                    <div class="sf-shortcut-label">Gerenciar Unidades</div>
                    <div class="sf-shortcut-desc">UBSs e postos</div>
                </div>
            </a>
        </div>

    </div>

    <!-- ===== CONSULTAS RECENTES ===== -->
    <div class="sf-card card p-4 no-hover">
        <h5 class="fw-bold mb-3 text-sf-primary">
            <i class="bi bi-list-check me-2"></i>Consultas Recentes
        </h5>
        <div class="table-responsive">
            <table class="table sf-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Especialidade</th>
                        <th>Médico</th>
                        <th>Unidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentes as $c): ?>
                    <tr>
                        <td class="text-muted small"><?= $c['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($c['paciente']) ?></td>
                        <td><?= date('d/m/Y', strtotime($c['data'])) ?></td>
                        <td><?= substr($c['hora'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($c['especialidade']) ?></td>
                        <td class="small"><?= htmlspecialchars($c['medico']) ?></td>
                        <td class="small"><?= htmlspecialchars($c['unidade']) ?></td>
                        <td>
                            <span class="badge badge-<?= $c['status'] ?> px-3 py-2">
                                <?= ucfirst($c['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x me-2"></i>Nenhuma consulta registrada ainda.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</section>

<?php require '../includes/footer.php'; ?>
