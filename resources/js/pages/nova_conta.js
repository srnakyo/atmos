import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-nova-conta');
    const btnAbrir = document.getElementById('nova-conta');
    const btnFechar = document.getElementById('cancelar-modal');
    const form = document.getElementById('form-nova-conta');
    const emailInput = document.getElementById('email_nike');
    const token = document.querySelector('input[name="_token"]').value;

    let emailValido = false;
    let debounceTimer = null;

    const mostrarErro = (mensagem) => {
        let erroSpan = document.getElementById('email-error');
        if (!erroSpan) {
            erroSpan = document.createElement('span');
            erroSpan.id = 'email-error';
            erroSpan.className = 'text-sm text-red-500 mt-1 block';
            emailInput.insertAdjacentElement('afterend', erroSpan);
        }
        erroSpan.textContent = mensagem;
        emailInput.classList.add('border-red-500');
    };

    const limparErro = () => {
        const erroSpan = document.getElementById('email-error');
        if (erroSpan) erroSpan.remove();
        emailInput.classList.remove('border-red-500');
    };

    const validarEmail = async (email) => {
        if (!email) {
            emailValido = false;
            limparErro();
            return;
        }

        try {
            const response = await axios.post('/contas/verificar', { email_nike: email }, {
                headers: { 'X-CSRF-TOKEN': token }
            });

            if (response.data.exists) {
                emailValido = false;
                mostrarErro('E-mail já cadastrado.');
            } else {
                emailValido = true;
                limparErro();
            }
        } catch (error) {
            emailValido = false;
            Swal.fire('Erro', 'Não foi possível validar o e-mail.', 'error');
        }
    };

    const fecharModal = () => {
        modal.classList.add('hidden');
    };

    btnAbrir.addEventListener('click', () => {
        modal.classList.remove('hidden');
        emailInput.value = '';
        limparErro();
        emailValido = false;
    });

    btnFechar.addEventListener('click', fecharModal);

    emailInput.addEventListener('input', () => {
        const email = emailInput.value.trim();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            validarEmail(email);
        }, 400);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();

        if (!email) {
            mostrarErro('O campo de e-mail é obrigatório.');
            return;
        }

        if (!emailValido) {
            mostrarErro('E-mail inválido ou já cadastrado.');
            return;
        }

        try {
            await axios.post('/contas', { email_nike: email }, {
                headers: { 'X-CSRF-TOKEN': token }
            });

            Swal.fire('Sucesso', 'Conta Nike adicionada com sucesso.', 'success')
                .then(() => location.reload());

        } catch (error) {
            Swal.fire('Erro', error.response?.data?.message || 'Erro ao adicionar conta.', 'error');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            fecharModal();
        }
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharModal();
        }
    });
});
