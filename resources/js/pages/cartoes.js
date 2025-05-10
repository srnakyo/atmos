import Swal from 'sweetalert2';
import axios from 'axios';
import IMask from 'imask';


window.deletarCartao = function (id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#78c676',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, remover',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/cartoes/${id}`)
            .then(() => {
                Swal.fire('Removido!', 'O cartão foi deletado.', 'success')
                .then(() => location.reload());
            })
            .catch(() => {
                Swal.fire('Erro', 'Não foi possível remover o cartão.', 'error');
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-cartoes');
    const openModalBtn = document.querySelector('.text-greenlight.flex');
    const closeModalBtn = document.getElementById('fechar-modal');
    const addCardBtn = document.getElementById('add-card-form');
    const form = document.getElementById('form-cartoes');

    function carregarBandeiras(targetSelect) {
        fetch('/cartoes/marcas')
        .then(response => response.json())
        .then(brands => {
            targetSelect.innerHTML = '';
            brands.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand.name;
                option.textContent = brand.name;
                targetSelect.appendChild(option);
            });
        });
    }

    function aplicarMascaras(cardElement) {
        const numeroInput = cardElement.querySelector('input[name="numero[]"]');
        const cvvInput = cardElement.querySelector('input[name="cvv[]"]');

        if (numeroInput) {
            IMask(numeroInput, { mask: '0000 0000 0000 0000' });
        }
        if (cvvInput) {
            IMask(cvvInput, { mask: '000' });
        }
    }

    function limparErros(cardElement) {
        cardElement.querySelectorAll('.erro-campo').forEach(span => span.remove());
        cardElement.querySelectorAll('input, select').forEach(input => {
            input.classList.remove('border-red-500');
        });
    }

    function exibirErro(input, mensagem) {
        input.classList.add('border-red-500');
        let erroSpan = document.createElement('span');
        erroSpan.className = 'erro-campo text-red-500 text-xs mt-1 block';
        erroSpan.textContent = mensagem;
        input.insertAdjacentElement('afterend', erroSpan);
    }

    openModalBtn.addEventListener('click', e => {
        e.preventDefault();
        modal.classList.remove('hidden');

        const oldCards = form.querySelectorAll('.form-cartao');
        oldCards.forEach(c => c.remove());

        const firstForm = document.createElement('div');
        firstForm.className = 'form-cartao grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 border-b border-gray-700 pb-6';

        firstForm.innerHTML = `
        <div class="col-span-3 md:col-span-1">
            <label class="text-sm text-gray-800 block mb-1">Nome do Titular</label>
            <input type="text" name="nome_titular[]" class="w-full bg-bg text-white p-2 rounded">
        </div>
        <div class="col-span-3 md:col-span-1">
            <label class="text-sm text-gray-800 block mb-1">Número</label>
            <input type="text" name="numero[]" maxlength="19" class="w-full bg-bg text-white p-2 rounded">
        </div>
        <div class="col-span-3 md:col-span-1">
            <label class="text-sm text-gray-800 block mb-1">Bandeira</label>
            <select name="bandeira[]" class="brand-select w-full bg-bg text-white p-2 rounded">
                <option value="">Selecione</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-800 block mb-1">Mês</label>
            <select name="validade_mes[]" class="w-full bg-bg text-white p-2 rounded">
            ${Array.from({ length: 12 }, (_, i) => `<option value="${String(i + 1).padStart(2, '0')}">${String(i + 1).padStart(2, '0')}</option>`).join('')}
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-800 block mb-1">Ano</label>
            <select name="validade_ano[]" class="w-full bg-bg text-white p-2 rounded">
                ${Array.from({ length: 11 }, (_, i) => {
                    const year = new Date().getFullYear() + i;
                    return `<option value="${year}">${year}</option>`;
                }).join('')}
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-800 block mb-1">CVV</label>
            <input type="text" name="cvv[]" maxlength="3" class="w-20 bg-bg text-white p-2 rounded">
        </div>
        `;

        form.appendChild(firstForm);

        const novaSelect = firstForm.querySelector('.brand-select');
        if (novaSelect) carregarBandeiras(novaSelect);

        aplicarMascaras(firstForm);
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', e => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });

    addCardBtn.addEventListener('click', () => {
        const firstForm = form.querySelector('.form-cartao');
        const clone = firstForm.cloneNode(true);

        clone.querySelectorAll('input, select').forEach(input => input.value = '');

        const removerBtn = document.createElement('button');
        removerBtn.type = 'button';
        removerBtn.className = 'text-red-500 text-sm font-semibold cursor-pointer remove-card-btn col-span-3 text-right';
        removerBtn.innerText = 'Remover';

        removerBtn.addEventListener('click', () => {
            const allCards = form.querySelectorAll('.form-cartao');
            if (allCards.length > 1) {
                clone.remove();
            }
        });

        clone.appendChild(removerBtn);
        form.appendChild(clone);

        const novaSelect = clone.querySelector('.brand-select');
        if (novaSelect) carregarBandeiras(novaSelect);

        aplicarMascaras(clone);
    });

    document.getElementById('salvar-cartoes').addEventListener('click', () => {
        let valid = true;
        const cardForms = form.querySelectorAll('.form-cartao');

        cardForms.forEach(card => {
            limparErros(card);

            card.querySelectorAll('input, select').forEach(input => {
                if (input.value.trim() === '' || input.value === 'Selecione') {
                    exibirErro(input, 'Campo obrigatório');
                    valid = false;
                }
            });
        });

        if (!valid) {
            return;
        }

        form.querySelectorAll('input[name="numero[]"]').forEach(input => {
            input.value = input.value.replace(/\s/g, '');
        });

        const formData = new FormData(form);
        fetch('/cartoes/multiplos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cartão adicionado!',
                    text: 'Cartão salvo com sucesso.',
                    confirmButtonColor: '#78c676',
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: data.message || 'Erro ao salvar cartões.',
                    confirmButtonColor: '#e3342f',
                });
            }
        }).catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao salvar cartões.',
                confirmButtonColor: '#e3342f',
            });
        });
    });
});




