import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', async e => {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos obrigatórios',
                text: 'Preencha o e-mail e a senha.',
                confirmButtonColor: '#78c676',
            });
            return;
        }

        try {
            const response = await axios.post('/login', {
                email,
                password,
            }, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            });

            window.location.href = '/contas';
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao entrar',
                text: 'E-mail ou senha inválidos.',
                confirmButtonColor: '#78c676',
            });
        }
    });
});
