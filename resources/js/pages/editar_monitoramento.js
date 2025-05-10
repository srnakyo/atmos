import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    let selectedEditSizes = new Set();
    let skuReverseMap = {};

    function atualizarHiddenTamanhosEditar() {
        document.getElementById('monitoramento-tamanhosSelecionados').value = [...selectedEditSizes].join(';');
    }

    function calcularSkusSelecionados() {
        const skusSelecionados = Object.entries(skuReverseMap)
            .filter(([sku, tamanho]) => selectedEditSizes.has(tamanho))
            .map(([sku]) => sku);
        return skusSelecionados.join(',');
    }

    document.querySelectorAll('.btn-editar-monitoring').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.getAttribute('data-id');

            try {
                const response = await axios.get(`/monitoramento/${id}`);
                const monitoramento = response.data;

                document.getElementById('monitoramento-id').value = monitoramento.id;
                document.getElementById('monitoramento-nome').textContent = monitoramento.nome;
                document.getElementById('monitoramento-foto').src = monitoramento.foto;
                document.getElementById('monitoramento-parcelas').value = monitoramento.parcelas;
                document.getElementById('monitoramento-tamanhosSelecionados').value = monitoramento.tamanho;

                selectedEditSizes = new Set(monitoramento.tamanho.split(';').map(t => t.trim()));

                const container = document.getElementById('monitoramento-grade-tamanhos');
                container.innerHTML = '';

                skuReverseMap = {};
                const allSkuParsed = JSON.parse(monitoramento.all_sku || '{}');
                Object.entries(allSkuParsed).forEach(([sku, tamanho]) => {
                    skuReverseMap[sku] = tamanho;
                });

                const tamanhosUnicos = [...new Set(Object.values(skuReverseMap))];

                tamanhosUnicos.forEach(tamanho => {
                    const div = document.createElement('div');
                    div.textContent = tamanho;
                    div.dataset.valor = tamanho;
                    div.className = 'text-white text-sm p-2 text-center rounded cursor-pointer hover:bg-green-700';

                    if (selectedEditSizes.has(tamanho)) {
                        div.classList.add('bg-green-600');
                    } else {
                        div.classList.add('bg-gray-800');
                    }

                    div.addEventListener('click', () => {
                        if (selectedEditSizes.has(tamanho)) {
                            selectedEditSizes.delete(tamanho);
                            div.classList.remove('bg-green-600');
                            div.classList.add('bg-gray-800');
                        } else {
                            selectedEditSizes.add(tamanho);
                            div.classList.remove('bg-gray-800');
                            div.classList.add('bg-green-600');
                        }
                        atualizarHiddenTamanhosEditar();
                    });

                    container.appendChild(div);
                });

                const select = document.getElementById('monitoramento-parcelas');
                select.innerHTML = '<option value="">Selecione</option>';
                const max = parseInt(monitoramento.parcelas_max ?? 12);
                for (let i = 1; i <= max; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `${i}x`;
                    if (parseInt(monitoramento.parcelas) === i) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                }

                document.getElementById('modal-monitoramento-edicao').classList.remove('hidden');
            } catch (err) {
                Swal.fire('Erro', 'Erro ao carregar monitoramento.', 'error');
            }
        });
    });

    document.getElementById('btn-monitoramento-salvar').addEventListener('click', async () => {
        const id = document.getElementById('monitoramento-id').value;
        const parcelas = document.getElementById('monitoramento-parcelas').value;
        const tamanhos = document.getElementById('monitoramento-tamanhosSelecionados').value;
        const sku = calcularSkusSelecionados();

        if (!parcelas || !tamanhos || !sku) {
            Swal.fire('Erro', 'Preencha os campos obrigatórios.', 'error');
            return;
        }

        try {
            await axios.put(`/monitoramento/${id}`, {
                parcelas,
                tamanhos,
                sku
            });

            Swal.fire('Sucesso', 'Monitoramento atualizado com sucesso.', 'success').then(() => {
                location.reload();
            });
        } catch (err) {
            Swal.fire('Erro', 'Não foi possível salvar as alterações.', 'error');
        }
    });
});
