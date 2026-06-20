<?php
$pageTitle = 'Unidades de Saúde';
require '../includes/conexao.php';
require '../includes/header.php';

$pdo = conectar();
$unidades = $pdo->query(
    "SELECT u.*, COUNT(m.id) AS total_medicos
     FROM unidades u
     LEFT JOIN medicos m ON m.unidade_id = u.id
     WHERE u.ativo = 1
     GROUP BY u.id
     ORDER BY u.nome"
)->fetchAll();
?>

<section class="sf-section">
<div class="container">
    <div class="text-center mb-5">
        <h2 class="sf-section-title fw-bold">Unidades de Saúde</h2>
        <p class="sf-section-sub">Encontre a unidade mais próxima de você em Passo Fundo</p>
    </div>
    <div class="row g-4">
        <?php foreach ($unidades as $u): ?>
        <div class="col-md-6 col-lg-3 sf-fade-in">
            <div class="sf-card card h-100 p-4">
                <div class="card-icon mb-3"><i class="bi bi-building-fill-cross"></i></div>
                <h5 class="fw-bold"><?= htmlspecialchars($u['nome']) ?></h5>
                <p class="text-muted small mb-1">
                    <i class="bi bi-geo-alt me-1 text-sf-primary"></i><?= htmlspecialchars($u['endereco']) ?>
                </p>
                <p class="text-muted small mb-1">
                    <i class="bi bi-map me-1 text-sf-primary"></i><?= htmlspecialchars($u['bairro']) ?> – <?= htmlspecialchars($u['cidade']) ?>
                </p>
                <?php if ($u['telefone']): ?>
                <p class="text-muted small mb-2">
                    <i class="bi bi-telephone me-1 text-sf-primary"></i><?= htmlspecialchars($u['telefone']) ?>
                </p>
                <?php endif; ?>
                <p class="small mb-3">
                    <span class="badge bg-sf-primary text-white">
                        <i class="bi bi-person-badge me-1"></i><?= $u['total_medicos'] ?> médico(s)
                    </span>
                </p>
                <a href="agendar.php?unidade=<?= $u['id'] ?>" class="btn btn-sf-primary btn-sm mt-auto">
                    <i class="bi bi-calendar2-plus me-1"></i>Agendar nesta unidade
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</section>

<?php require '../includes/footer.php'; ?>
