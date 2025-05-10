import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.remover-conta').forEach(button => {
        button.addEventListener('click', async () => {
            const contaId = button.dataset.id;
            const token = document.querySelector('meta[name="csrf-token"]').content;

            const confirmar = await Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#78c676',
                cancelButtonColor: '#d33',
            });

            if (confirmar.isConfirmed) {
                try {
                    await axios.delete(`/contas/remover/${contaId}`, {
                        headers: { 'X-CSRF-TOKEN': token }
                    });

                    Swal.fire('Removida', 'Conta excluída com sucesso.', 'success')
                        .then(() => location.reload());
                } catch (error) {
                    Swal.fire('Erro', 'Não foi possível remover a conta.', 'error');
                }
            }
        });
    });
});
