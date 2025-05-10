import Swal from 'sweetalert2';
import axios from 'axios';


document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('btn-vincular');
    const closeBtn = document.getElementById('fechar-modal-vincular');
    const modal = document.getElementById('modal-vincular');

    if (openBtn && closeBtn && modal) {
        openBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-editar-conta');
    const emailInput = document.getElementById('email_nike');
    const contaId = emailInput.dataset.id;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let emailValido = true;
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
            const response = await axios.post('/contas/verificar', {
                email_nike: email,
                id: contaId
            });

            if (response.data.exists) {
                emailValido = false;
                mostrarErro('E-mail já cadastrado.');
            } else {
                emailValido = true;
                limparErro();
            }
        } catch {
            emailValido = false;
            Swal.fire('Erro', 'Não foi possível validar o e-mail.', 'error');
        }
    };

    emailInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            validarEmail(emailInput.value.trim());
        }, 400);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();

    // Revalida antes de continuar
        await validarEmail(email);

        if (!email || !emailValido) {
            mostrarErro('E-mail inválido ou já cadastrado.');
            return;
        }

        try {
            await axios.put(`/contas/atualizar/${contaId}`, {
                email_nike: email,
                sincronizado: null
            }, {
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });

            await Swal.fire('Sucesso', 'Conta atualizada com sucesso.', 'success');
            location.reload();

        } catch (error) {
            Swal.fire('Erro', error.response?.data?.message || 'Erro ao atualizar a conta.', 'error');
        }
    });

});



document.querySelectorAll('.remover-cartao').forEach(botao => {
    botao.addEventListener('click', async () => {
        const contaId = botao.dataset.conta;
        const cartaoId = botao.dataset.cartao;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const confirmar = await Swal.fire({
            title: 'Remover cartão?',
            text: 'Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#78c676',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        });

        if (confirmar.isConfirmed) {
            try {
                await axios.delete(`/contas/${contaId}/cartao/${cartaoId}`, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                await Swal.fire('Removido', 'Cartão desvinculado com sucesso.', 'success');
                location.reload();
            } catch (error) {
                Swal.fire('Erro', 'Não foi possível remover o cartão.', 'error');
            }
        }
    });
});
