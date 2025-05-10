import Swal from 'sweetalert2';
import axios from 'axios';

document.querySelectorAll('.btn-editar-cartao').forEach(btn => {
    btn.addEventListener('click', async () => {
        const modal = document.getElementById('modal-editar-cartao');

        document.getElementById('cartao_id').value = btn.dataset.id;
        document.getElementById('nome_titular').value = btn.dataset.nome;
        document.getElementById('numero').value = btn.dataset.numero;

        const bandeiraSelect = document.getElementById('bandeira');
        const mesSelect = document.getElementById('validade_mes');
        const anoSelect = document.getElementById('validade_ano');

        const response = await fetch('/cartoes/marcas');
        const brands = await response.json();
        bandeiraSelect.innerHTML = '';
        brands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.name;
            option.textContent = brand.name;
            if (brand.name === btn.dataset.bandeira) {
                option.selected = true;
            }
            bandeiraSelect.appendChild(option);
        });

        mesSelect.innerHTML = '';
        for (let i = 1; i <= 12; i++) {
            const val = String(i).padStart(2, '0');
            const option = document.createElement('option');
            option.value = val;
            option.textContent = val;
            if (val === btn.dataset.mes.padStart(2, '0')) option.selected = true;
            mesSelect.appendChild(option);
        }

        anoSelect.innerHTML = '';
        const anoAtual = new Date().getFullYear();
        for (let i = 0; i <= 10; i++) {
            const val = String(anoAtual + i);
            const option = document.createElement('option');
            option.value = val;
            option.textContent = val;
            if (val === btn.dataset.ano) option.selected = true;
            anoSelect.appendChild(option);
        }

        document.getElementById('cvv').value = btn.dataset.cvv;

        IMask(document.getElementById('numero'), { mask: '0000 0000 0000 0000' });
        IMask(document.getElementById('cvv'), { mask: '000' });

        modal.classList.remove('hidden');
    });
});

document.getElementById('cancelar-edicao').addEventListener('click', () => {
    document.getElementById('modal-editar-cartao').classList.add('hidden');
});

document.getElementById('modal-editar-cartao').addEventListener('click', (e) => {
    if (e.target.id === 'modal-editar-cartao') {
        document.getElementById('modal-editar-cartao').classList.add('hidden');
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modal-editar-cartao');
        if (!modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    }
});

document.getElementById('form-editar-cartao').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('cartao_id').value;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const data = {
        nome_titular: document.getElementById('nome_titular').value.trim(),
        numero: document.getElementById('numero').value.replace(/\s/g, ''),
        bandeira: document.getElementById('bandeira').value,
        validade_mes: document.getElementById('validade_mes').value,
        validade_ano: document.getElementById('validade_ano').value,
        cvv: document.getElementById('cvv').value
    };

    try {
        await axios.put(`/cartoes/${id}`, data, {
            headers: {
                'X-CSRF-TOKEN': token
            }
        });

        await Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Cartão atualizado com sucesso.',
            confirmButtonColor: '#78c676'
        });

        location.reload();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: error.response?.data?.message || 'Erro ao atualizar o cartão.',
            confirmButtonColor: '#e3342f'
        });
    }
});
