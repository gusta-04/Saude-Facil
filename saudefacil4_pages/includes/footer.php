<?php $basePath = $basePath ?? ((strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : ''); ?>
<!-- FOOTER -->
<footer class="sf-footer mt-5">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="sf-logo-icon small"><i class="bi bi-hospital-fill"></i></span>
                    <span class="sf-logo-text text-white">Saúde<strong>Fácil</strong></span>
                </div>
                <p class="text-white-50 small">Sistema de agendamento de consultas médicas para a rede pública de saúde. Projeto Integrador – Banco de Dados | Linguagem Web | Análise de Projeto.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-3">Links Rápidos</h6>
                <ul class="list-unstyled small">
                    <li><a href="<?= $basePath ?>index.php" class="text-white-50 text-decoration-none"><i class="bi bi-chevron-right me-1"></i>Início</a></li>
                    <li><a href="<?= $basePath ?>pages/unidades.php" class="text-white-50 text-decoration-none"><i class="bi bi-chevron-right me-1"></i>Unidades de Saúde</a></li>
                    <li><a href="<?= $basePath ?>pages/agendar.php" class="text-white-50 text-decoration-none"><i class="bi bi-chevron-right me-1"></i>Agendar Consulta</a></li>
                    <li><a href="<?= $basePath ?>pages/cadastro.php" class="text-white-50 text-decoration-none"><i class="bi bi-chevron-right me-1"></i>Cadastre-se</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-3">Professores Orientadores</h6>
                <ul class="list-unstyled small text-white-50">
                    <li><i class="bi bi-person-badge me-1"></i>Prof. Celso – Banco de Dados</li>
                    <li><i class="bi bi-person-badge me-1"></i>Prof. Marcos – Linguagem Web</li>
                    <li><i class="bi bi-person-badge me-1"></i>Prof. Naura – Análise de Projeto</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center text-white-50 small mb-0">&copy; <?= date('Y') ?> SaúdeFácil – Projeto Integrador | Todos os direitos reservados.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>js/main.js"></script>
</body>
</html>
