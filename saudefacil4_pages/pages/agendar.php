<?php
$pageTitle = 'Agendar Consulta';
require '../includes/conexao.php';
require '../includes/auth.php';
requerLogin();

$pdo = conectar();
$user = usuarioLogado();
$erro = '';
$sucesso = '';

// Processar agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agenda_id = (int)($_POST['agenda_id'] ?? 0);
    $obs = trim($_POST['observacao'] ?? '');

    if (!$agenda_id) {
        $erro = 'Selecione um horário disponível.';
    } else {
        // Verificar se já tem consulta neste horário
        $dup = $pdo->prepare(
            "SELECT id FROM consultas WHERE paciente_id=? AND agenda_id=? AND status='agendada'"
        );
        $dup->execute([$user['id'], $agenda_id]);
        if ($dup->fetch()) {
            $erro = 'Você já possui uma consulta neste horário.';
        } else {
            // Verificar vagas disponíveis
            $vagaStmt = $pdo->prepare(
                "SELECT a.vagas,
                        (SELECT COUNT(*) FROM consultas c WHERE c.agenda_id=a.id AND c.status='agendada') as ocupadas
                 FROM agenda a WHERE a.id=?"
            );
            $vagaStmt->execute([$agenda_id]);
            $vaga = $vagaStmt->fetch();
            if (!$vaga || $vaga['vagas'] <= $vaga['ocupadas']) {
                $erro = 'Este horário não possui mais vagas disponíveis.';
            } else {
                $ins = $pdo->prepare(
                    "INSERT INTO consultas (paciente_id, agenda_id, observacao) VALUES (?,?,?)"
                );
                $ins->execute([$user['id'], $agenda_id, $obs ?: null]);
                $sucesso = 'Consulta agendada com sucesso!';
            }
        }
    }
}

// Carregar filtros
$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome")->fetchAll();
$unidades       = $pdo->query("SELECT * FROM unidades WHERE ativo=1 ORDER BY nome")->fetchAll();

$filtroEsp   = (int)($_GET['especialidade'] ?? $_POST['especialidade'] ?? 0);
$filtroUnid  = (int)($_GET['unidade']       ?? $_POST['unidade']       ?? 0);
$filtroData  = $_GET['data'] ?? $_POST['data'] ?? '';

// Buscar horários disponíveis
$horarios = [];
if ($filtroEsp || $filtroUnid || $filtroData) {
    $sql = "SELECT a.id, a.data, a.hora, a.vagas,
                   m.nome AS medico, e.nome AS especialidade, u.nome AS unidade,
                   (SELECT COUNT(*) FROM consultas c WHERE c.agenda_id=a.id AND c.status='agendada') AS ocupadas
            FROM agenda a
            JOIN medicos m ON m.id = a.medico_id
            JOIN especialidades e ON e.id = m.especialidade_id
            JOIN unidades u ON u.id = m.unidade_id
            WHERE a.data >= CURDATE()";
    $params = [];
    if ($filtroEsp)  { $sql .= " AND e.id = ?"; $params[] = $filtroEsp; }
    if ($filtroUnid) { $sql .= " AND u.id = ?"; $params[] = $filtroUnid; }
    if ($filtroData) { $sql .= " AND a.data = ?"; $params[] = $filtroData; }
    $sql .= " ORDER BY a.data, a.hora";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $horarios = $stmt->fetchAll();
}

require '../includes/header.php';
?>

<section class="sf-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="sf-section-title fw-bold">Agendar Consulta</h2>
            <p class="sf-section-sub">Filtre pela especialidade, unidade ou data desejada</p>
        </div>

        <?php if ($erro): ?>
        <div class="alert alert-danger sf-flash alert-dismissible" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
        <div class="alert alert-success sf-flash alert-dismissible" role="alert">
            <i class="bi bi-check2-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
            <a href="minhas_consultas.php" class="alert-link ms-2">Ver minhas consultas →</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Filtros -->
        <form method="GET" action="agendar.php" class="sf-form-card card mb-5">
            <h5 class="fw-bold mb-4 text-sf-primary"><i class="bi bi-funnel me-2"></i>Filtrar Horários</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Especialidade</label>
                    <select name="especialidade" class="form-select">
                        <option value="">Todas as especialidades</option>
                        <?php foreach ($especialidades as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $filtroEsp==$e['id']?'selected':'' ?>>
                            <?= htmlspecialchars($e['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Unidade de Saúde</label>
                    <select name="unidade" class="form-select">
                        <option value="">Todas as unidades</option>
                        <?php foreach ($unidades as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filtroUnid==$u['id']?'selected':'' ?>>
                            <?= htmlspecialchars($u['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data</label>
                    <input type="date" name="data" class="form-control"
                           min="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($filtroData) ?>">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-sf-primary px-4">
                    <i class="bi bi-search me-2"></i>Buscar Horários
                </button>
                <a href="agendar.php" class="btn btn-outline-secondary ms-2">Limpar</a>
            </div>
        </form>

        <!-- Resultados -->
        <?php if ($filtroEsp || $filtroUnid || $filtroData): ?>
            <?php if (empty($horarios)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size:3rem;"></i>
                <p class="mt-3 text-muted">Nenhum horário disponível para o filtro selecionado.</p>
            </div>
            <?php else: ?>
            <form method="POST" action="agendar.php">
                <input type="hidden" name="especialidade" value="<?= $filtroEsp ?>">
                <input type="hidden" name="unidade" value="<?= $filtroUnid ?>">
                <input type="hidden" name="data" value="<?= htmlspecialchars($filtroData) ?>">
                <input type="hidden" id="agenda_id" name="agenda_id" value="">

                <h5 class="fw-bold mb-3 text-sf-primary">
                    <i class="bi bi-calendar2-check me-2"></i>
                    <?= count($horarios) ?> horário(s) encontrado(s) – clique para selecionar
                </h5>

                <?php
                $porData = [];
                foreach ($horarios as $h) { $porData[$h['data']][] = $h; }
                foreach ($porData as $data => $slots):
                    $dataFmt = date('d/m/Y (l)', strtotime($data));
                    $diasPt  = ['Sunday'=>'Domingo','Monday'=>'Segunda','Tuesday'=>'Terça',
                                'Wednesday'=>'Quarta','Thursday'=>'Quinta','Friday'=>'Sexta','Saturday'=>'Sábado'];
                    foreach ($diasPt as $en => $pt) $dataFmt = str_replace($en, $pt, $dataFmt);
                ?>
                <div class="sf-card card p-4 mb-4">
                    <h6 class="fw-bold text-sf-primary mb-3">
                        <i class="bi bi-calendar3 me-2"></i><?= $dataFmt ?>
                    </h6>
                    <div class="row g-3">
                        <?php foreach ($slots as $s):
                            $disponivel = $s['vagas'] > $s['ocupadas'];
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="sf-slot <?= $disponivel ? '' : 'indisponivel' ?>"
                                 <?= $disponivel ? "data-agenda='{$s['id']}'" : '' ?>>
                                <div class="fw-bold"><?= substr($s['hora'],0,5) ?></div>
                                <div class="small"><?= htmlspecialchars($s['medico']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($s['especialidade']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($s['unidade']) ?></div>
                                <?php if (!$disponivel): ?>
                                <span class="badge bg-secondary mt-1">Lotado</span>
                                <?php else: ?>
                                <span class="small text-success"><i class="bi bi-check-circle-fill"></i> Disponível</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="sf-form-card card mt-4">
                    <h6 class="fw-bold mb-3">Observações (opcional)</h6>
                    <textarea name="observacao" class="form-control" rows="3"
                              placeholder="Informe sintomas, alergias ou outras informações relevantes..."></textarea>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sf-primary px-5 py-2">
                            <i class="bi bi-calendar2-check me-2"></i>Confirmar Agendamento
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require '../includes/footer.php'; ?>
