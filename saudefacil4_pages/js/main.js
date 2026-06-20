// js/main.js – SaúdeFácil

// Seleção de horário na tela de agendamento
document.querySelectorAll('.sf-slot:not(.indisponivel)').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sf-slot').forEach(s => s.classList.remove('selected'));
        btn.classList.add('selected');
        const input = document.getElementById('agenda_id');
        if (input) input.value = btn.dataset.agenda;
    });
});

// Confirmação de cancelamento
document.querySelectorAll('.btn-cancelar').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm('Deseja realmente cancelar esta consulta?')) e.preventDefault();
    });
});

// Fechar alertas automaticamente
setTimeout(() => {
    document.querySelectorAll('.sf-flash').forEach(el => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    });
}, 4000);
