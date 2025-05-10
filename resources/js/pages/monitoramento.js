import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const btnAdicionar = document.getElementById('btn-adicionar');
    const modal = document.getElementById('modal-monitoramento');
    const inputLink = document.getElementById('input-link');
    const btnBuscar = document.getElementById('btn-buscar');
    const produtoNome = document.getElementById('produto-nome');
    const produtoFoto = document.getElementById('produto-foto');
    const gradeTamanhos = document.getElementById('grade-tamanhos');
    const conteudoProduto = document.getElementById('conteudo-produto');
    const selectParcelas = document.getElementById('produto-parcelas');

    const hiddenNome = document.getElementById('produto-nome-hidden');
    const hiddenFoto = document.getElementById('produto-foto-url');
    const hiddenCodigoEstilo = document.getElementById('produto-codigo_estilo');
    const hiddenTamanhos = document.getElementById('tamanhosSelecionados');

    let selectedSizes = new Set();
    let skuMap = {};
    let parcelasMax = 1;

    btnAdicionar.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    btnBuscar.addEventListener('click', () => {
        const url = inputLink.value.trim();
        if (url) buscarProduto(url);
    });

    document.getElementById('btn-selecionar-todos').addEventListener('click', () => {
        document.querySelectorAll('#grade-tamanhos div').forEach(div => {
            selectedSizes.add(div.dataset.valor);
            div.classList.add('bg-green-600');
        });
        atualizarHiddenTamanhos();
    });

    document.getElementById('btn-limpar-selecao').addEventListener('click', () => {
        selectedSizes.clear();
        document.querySelectorAll('#grade-tamanhos div').forEach(div => {
            div.classList.remove('bg-green-600');
        });
        atualizarHiddenTamanhos();
    });

    async function buscarProduto(url) {
        Swal.fire({
            title: 'Buscando produto...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await axios.post('/produto/monitorar', { link: url });
            const product = response.data?.json?.props?.pageProps?.product;

            Swal.close();

            if (!product || !product.name || !product.sizes?.length) {
                Swal.fire('Erro', 'Produto não encontrado ou dados incompletos.', 'error');
                return;
            }

            const nome = product.name;
            const codigoEstilo = product.colorInfo?.styleCode || '';
            const codigoCor = product.colorInfo?.code || '';
            const codigoModelo = product.code || '';
            const foto = `https://imgnike-a.akamaihd.net/1300x1300/${codigoModelo}${codigoCor}.jpg`;

            produtoNome.textContent = nome;
            produtoFoto.src = foto;
            conteudoProduto.classList.remove('hidden');

            hiddenNome.value = nome;
            hiddenFoto.value = foto;
            hiddenCodigoEstilo.value = codigoEstilo;

            gradeTamanhos.innerHTML = '';
            selectedSizes.clear();
            skuMap = {};

            product.sizes.forEach(item => {
                const tamanho = item?.description;
                const sku = item?.sku;
                if (!tamanho || !sku) return;

                skuMap[tamanho] = sku;

                const div = document.createElement('div');
                div.textContent = tamanho;
                div.className = 'bg-gray-800 text-white text-sm p-2 text-center rounded cursor-pointer hover:bg-green-700';
                div.dataset.valor = tamanho;
                div.dataset.sku = sku;

                div.addEventListener('click', () => {
                    if (selectedSizes.has(tamanho)) {
                        selectedSizes.delete(tamanho);
                        div.classList.remove('bg-green-600');
                    } else {
                        selectedSizes.add(tamanho);
                        div.classList.add('bg-green-600');
                    }
                    atualizarHiddenTamanhos();
                });

                gradeTamanhos.appendChild(div);
            });

            selectParcelas.innerHTML = '<option value="">Selecione</option>';
            parcelasMax = 1;
            if (product.installments?.length) {
                parcelasMax = Math.max(...product.installments.map(p => parseInt(p.quantity)));
                product.installments.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.quantity;
                    option.textContent = `${p.quantity}x de R$ ${p.valueFormatted}`;
                    selectParcelas.appendChild(option);
                });
            }

        } catch (err) {
            console.error(err);
            Swal.close();
            Swal.fire('Erro', 'Não foi possível buscar o produto.', 'error');
        }
    }

    function atualizarHiddenTamanhos() {
        hiddenTamanhos.value = [...selectedSizes].join(';');
    }

    document.getElementById('btn-salvar-monitoramento').addEventListener('click', async () => {
        const nome = hiddenNome.value;
        const foto = hiddenFoto.value;
        const codigoEstilo = hiddenCodigoEstilo.value;
        const tamanhos = hiddenTamanhos.value;
        const parcelas = selectParcelas.value;
        const link = inputLink.value.trim();

        const tamanhosSelecionados = tamanhos.split(';').map(t => t.trim()).filter(Boolean);
        const tamanhosDisponiveis = Object.keys(skuMap).join(';');

        const selectedSkuArray = [];
        const allSkuObject = {};

        for (const [tamanho, sku] of Object.entries(skuMap)) {
            allSkuObject[sku] = tamanho;
            if (tamanhosSelecionados.includes(tamanho)) {
                selectedSkuArray.push(sku);
            }
        }

        const sku = selectedSkuArray.join(',');
        const allSku = JSON.stringify(allSkuObject);

        if (!nome || !codigoEstilo || !parcelas || selectedSkuArray.length === 0) {
            Swal.fire('Erro', 'Preencha todos os dados antes de salvar.', 'error');
            return;
        }

        Swal.fire({
            title: 'Salvando monitoramento...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            await axios.post('/monitoramento', {
                nome,
                foto,
                codigo_estilo: codigoEstilo,
                tamanhos,
                tamanhos_disponiveis: tamanhosDisponiveis,
                parcelas,
                all_sku: allSku,
                sku,
                link,
                parcelas_max: parcelasMax
            });

            Swal.close();

            Swal.fire('Sucesso', 'Monitoramento salvo com sucesso.', 'success').then(() => {
                location.reload();
            });
        } catch (err) {
            console.error(err);
            Swal.close();

            let message = 'Não foi possível salvar o monitoramento.';
            if (err.response && err.response.data && err.response.data.message) {
                message = err.response.data.message;
            }

            Swal.fire('Erro', message, 'error');
        }
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.classList.add('hidden');
        }
    });
});
