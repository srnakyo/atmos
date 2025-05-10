import IMask from 'imask';
import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-enviar-compra');

    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');

    const inputLink = document.getElementById('input-link');
    const inputApiSession = document.getElementById('api-session');
    const inputNomeTitular = document.getElementById('nome-titular');

    const inputNumeroCartao = document.getElementById('numero-cartao');
    const inputExpiracaoCartao = document.getElementById('expiracao-cartao');
    const inputCvvCartao = document.getElementById('cvv-cartao');

    const gradeTamanhos = document.getElementById('grade-tamanhos');
    const divTamanhos = document.getElementById('div-tamanhos');
    const selectParcelas = document.getElementById('parcelas-cartao');

    const produtoImagem = document.getElementById('produto-foto');
    const produtoNome = document.getElementById('produto-nome');

    let skuSelecionado = '';
    let tamanhoSelecionado = '';
    let parsedSession = null;
    let tamanhosDisponiveis = {};
    let parcelasMax = 1;

    IMask(inputNumeroCartao, { mask: '0000 0000 0000 0000' });
    IMask(inputExpiracaoCartao, { mask: '00/0000' });
    IMask(inputCvvCartao, { mask: '000' });

    window.nextStep = function () {
        const link = inputLink.value.trim();
        const apiSession = inputApiSession.value.trim();

        if (!link) {
            Swal.fire('Erro', 'Informe o link do produto.', 'error');
            return;
        }

        if (!apiSession) {
            Swal.fire('Erro', 'Informe a API Session.', 'error');
            return;
        }

        try {
            parsedSession = JSON.parse(apiSession);
        } catch (err) {
            Swal.fire('Erro', 'O campo API Session deve conter um Nike Session válido.', 'error');
            return;
        }

        if (!parsedSession.x_client_token || !parsedSession.accessToken || !parsedSession.user?.email) {
            Swal.fire('Erro', 'A sessão está incompleta: falta x_client_token, accessToken ou email.', 'error');
            return;
        }

        if (!parsedSession.accessTokenExpires) {
            Swal.fire('Erro', 'A sessão não contém o campo "accessTokenExpires".', 'error');
            return;
        }

        const expiresAt = new Date(parsedSession.accessTokenExpires);
        if (isNaN(expiresAt.getTime())) {
            Swal.fire('Erro', 'O campo "accessTokenExpires" não é uma data válida.', 'error');
            return;
        }

        const hoje = new Date();
        const dataLimite = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate(), 10, 3, 0);

        if (expiresAt < dataLimite) {
            Swal.fire('Erro', 'O accessToken expira antes do lançamento de hoje. Relogue para renovar o token.', 'error');
            return;
        }

        if (!skuSelecionado || !tamanhoSelecionado) {
            Swal.fire('Erro', 'Selecione o tamanho do produto antes de continuar.', 'error');
            return;
        }

        step1.classList.add('hidden');
        step2.classList.remove('hidden');
    };

    window.backStep = function () {
        step2.classList.add('hidden');
        step1.classList.remove('hidden');
    };

    document.getElementById('btn-buscar-produto').addEventListener('click', async () => {
        const link = inputLink.value.trim();
        if (!link) {
            Swal.fire('Erro', 'Informe o link do produto.', 'error');
            return;
        }

        Swal.fire({
            title: 'Buscando produto...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await axios.post('/produto/monitorar', { link: link });
            const product = response.data?.json?.props?.pageProps?.product;

            Swal.close();

            if (!product || !product.name || !product.sizes?.length) {
                Swal.fire('Erro', 'Produto não encontrado ou dados incompletos.', 'error');
                return;
            }

            tamanhosDisponiveis = {};
            skuSelecionado = '';
            tamanhoSelecionado = '';

            produtoImagem.src = product.images?.[0]?.url || '';
            produtoNome.textContent = product.name;

            gradeTamanhos.innerHTML = '';
            product.sizes.forEach(item => {
                if (!item?.sku || !item?.description) return;

                tamanhosDisponiveis[item.description] = item.sku;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'bg-gray-800 text-white text-sm p-2 rounded text-center hover:bg-green-700 transition';
                btn.textContent = item.description;
                btn.dataset.sku = item.sku;
                btn.dataset.tamanho = item.description;

                btn.addEventListener('click', () => {
                    document.querySelectorAll('#grade-tamanhos button').forEach(b => b.classList.remove('bg-green-600'));
                    btn.classList.add('bg-green-600');
                    skuSelecionado = item.sku;
                    tamanhoSelecionado = item.description;
                });

                gradeTamanhos.appendChild(btn);
            });

            divTamanhos.classList.remove('hidden');

            selectParcelas.innerHTML = '<option value="">Selecione</option>';
            parcelasMax = 1;
            if (product.installments?.length) {
                parcelasMax = Math.max(...product.installments.map(p => parseInt(p.quantity)));
                product.installments.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.quantity;
                    opt.textContent = `${p.quantity}x de R$ ${p.valueFormatted}`;
                    selectParcelas.appendChild(opt);
                });
            }

        } catch (err) {
            console.error(err);
            Swal.close();
            Swal.fire('Erro', 'Erro ao buscar o produto.', 'error');
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const nomeTitular = inputNomeTitular.value.trim();
        if (!nomeTitular) {
            Swal.fire('Erro', 'Informe o nome do titular.', 'error');
            return;
        }

        const bandeira = document.getElementById('bandeira-cartao').value;
        const numero = inputNumeroCartao.value.replace(/\s/g, '');
        const validade = inputExpiracaoCartao.value;
        const cvv = inputCvvCartao.value;
        const parcelas = parseInt(selectParcelas.value);

        const payload = {
            link: inputLink.value.trim(),
            sku: skuSelecionado,
            tamanho: tamanhoSelecionado,
            x_client_token: parsedSession.x_client_token,
            access_token: parsedSession.accessToken,
            email_nike: parsedSession.user?.email || '',
            cartao: {
                nome_titular: nomeTitular,
                bandeira,
                numero,
                validade,
                cvv,
                parcelas
            }
        };

        Swal.fire({
            title: 'Enviando compra...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch('/request/drop', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            Swal.close();

            if (response.ok) {
                Swal.fire('Sucesso', 'Compra enviada com sucesso.', 'success').then(() => location.reload());
            } else {
                Swal.fire('Erro', 'Erro ao enviar a compra.', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.close();
            Swal.fire('Erro', 'Erro de comunicação.', 'error');
        }
    });
});
