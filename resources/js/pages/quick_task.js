import axios from 'axios';
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-quick-task');
    const form = document.getElementById('form-quick-task');
    const inputSku = document.getElementById('quick-sku');
    const inputEmail = document.getElementById('quick-email-nike');
    const cancelarBtn = document.getElementById('cancelar-quick-task');
    const errorText = document.getElementById('quick-sku-error');

    document.querySelectorAll('.quick-task').forEach(button => {
        button.addEventListener('click', () => {
            const emailNike = button.getAttribute('data-id');
            inputEmail.value = emailNike;
            inputSku.value = '';
            inputSku.classList.remove('border-red-500');
            errorText.style.display = 'none';
            modal.classList.remove('hidden');
        });
    });

    cancelarBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    inputSku.addEventListener('input', () => {
        inputSku.classList.remove('border-red-500');
        errorText.style.display = 'none';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const sku = inputSku.value.trim();
        const email_nike = inputEmail.value;

        if (!sku) {
            inputSku.classList.add('border-red-500');
            errorText.style.display = 'block';
            return;
        }

        try {
            await axios.post('/quick-task', {
                sku,
                email_nike
            }, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            modal.classList.add('hidden');

            Swal.fire({
                icon: 'success',
                title: 'Enviado',
                text: 'Quick task registrada com sucesso!'
            });
        } catch (error) {
            modal.classList.add('hidden');

            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: error.response?.data?.erro || 'Erro ao registrar a quick task.'
            });
        }
    });
});
