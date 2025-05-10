import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-deletar-monitoring').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.getAttribute('data-id');

            const confirmacao = await Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            });

            if (confirmacao.isConfirmed) {
                try {
                    await axios.delete(`/monitoramento/${id}`);
                    Swal.fire('Removido!', 'Monitoramento excluído com sucesso.', 'success')
                        .then(() => location.reload());
                } catch (error) {
                    Swal.fire('Erro', 'Não foi possível remover o monitoramento.', 'error');
                }
            }
        });
    });
});
