<?php
$pageTitle = 'Início';
require 'includes/header.php';
require 'includes/conexao.php';

$pdo = conectar();

// Contar dados para estatísticas
$totalUnidades    = $pdo->query("SELECT COUNT(*) FROM unidades WHERE ativo=1")->fetchColumn();
$totalEspecial    = $pdo->query("SELECT COUNT(*) FROM especialidades")->fetchColumn();
$totalAgendadas   = $pdo->query("SELECT COUNT(*) FROM consultas WHERE status='agendada'")->fetchColumn();

$especialidades = $pdo->query("SELECT * FROM especialidades")->fetchAll();
?>

<!-- HERO -->
<section class="sf-hero">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge text-bg-warning fw-bold mb-3 px-3 py-2 fs-6">
                    <i class="bi bi-geo-alt-fill me-1"></i> Passo Fundo – RS
                </span>
                <h1 class="fw-bold mb-3">Cuide da sua saúde<br>sem filas e sem espera.</h1>
                <p class="lead mb-4">Agende consultas médicas na rede pública de saúde de forma simples, rápida e gratuita. Sem precisar sair de casa.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="pages/agendar.php" class="btn btn-sf-accent btn-lg px-4">
                        <i class="bi bi-calendar2-plus me-2"></i>Agendar Agora
                    </a>
                    <a href="pages/unidades.php" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-hospital-fill" style="color:#d99200;"></i>Ver Unidades
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="sf-hero-stats">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="stat-num"><?= $totalUnidades ?></div>
                            <div class="stat-label">Unidades de Saúde</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-num"><?= $totalEspecial ?></div>
                            <div class="stat-label">Especialidades</div>
                        </div>
                        <div class="col-4">
                            <div class="stat-num"><?= $totalAgendadas ?></div>
                            <div class="stat-label">Consultas Ativas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COMO FUNCIONA -->
<section class="sf-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="sf-section-title fw-bold">Como Funciona</h2>
            <p class="sf-section-sub">Três passos simples para garantir sua consulta</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 sf-fade-in">
                <div class="sf-step">
                    <div class="sf-step-num">1</div>
                    <div class="sf-card p-4 h-100">
                        <div class="card-icon mx-auto"><i class="bi bi-person-plus-fill"></i></div>
                        <h5 class="fw-bold">Cadastre-se</h5>
                        <p class="text-muted small">Crie sua conta com CPF e dados básicos. É gratuito e leva menos de 2 minutos.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 sf-fade-in">
                <div class="sf-step">
                    <div class="sf-step-num">2</div>
                    <div class="sf-card p-4 h-100">
                        <div class="card-icon mx-auto"><i class="bi bi-search-heart"></i></div>
                        <h5 class="fw-bold">Escolha a Consulta</h5>
                        <p class="text-muted small">Selecione a especialidade, a unidade de saúde e o horário disponível mais conveniente.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 sf-fade-in">
                <div class="sf-step">
                    <div class="sf-step-num">3</div>
                    <div class="sf-card p-4 h-100">
                        <div class="card-icon mx-auto sf-card-accent"><i class="bi bi-check2-circle"></i></div>
                        <h5 class="fw-bold">Confirme e Pronto!</h5>
                        <p class="text-muted small">Sua consulta é confirmada na hora. Gerencie e cancele pelo sistema quando precisar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ESPECIALIDADES -->
<section class="sf-section sf-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="sf-section-title fw-bold">Especialidades Disponíveis</h2>
            <p class="sf-section-sub">Atendimento nas principais áreas da saúde pública</p>
        </div>
        <div class="row g-3 justify-content-center">
            <?php foreach ($especialidades as $esp): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="pages/agendar.php?especialidade=<?= $esp['id'] ?>" class="text-decoration-none">
                    <div class="sf-card p-3 text-center h-100">
                        <div class="card-icon mx-auto mb-2" style="font-size:1.4rem;">
                            <i class="bi <?= htmlspecialchars($esp['icone']) ?>"></i>
                        </div>
                        <div class="fw-semibold small text-sf-primary"><?= htmlspecialchars($esp['nome']) ?></div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA FINAL -->
<section class="sf-section text-center">
    <div class="container">
        <h2 class="sf-section-title fw-bold mb-3">Pronto para agendar sua consulta?</h2>
        <p class="sf-section-sub mb-4">Acesso gratuito para toda a população de Passo Fundo e região.</p>
        <a href="pages/cadastro.php" class="btn btn-sf-primary btn-lg px-5 me-3">
            <i class="bi bi-person-plus me-2"></i>Criar Conta
        </a>
        <a href="pages/agendar.php" class="btn btn-outline-secondary btn-lg px-5">
            <i class="bi bi-calendar2-plus me-2"></i>Agendar
        </a>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
