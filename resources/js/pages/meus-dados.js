import axios from 'axios'
import Swal from 'sweetalert2'

document.addEventListener('DOMContentLoaded', function () {
  const formNickname = document.getElementById('form-nickname')
  const nicknameInput = document.getElementById('nickname')
  const nicknameFeedback = document.getElementById('nickname-feedback')
  const webhookInput = document.getElementById('webhook')
  const webhookFeedback = document.getElementById('webhook-feedback')

  const formSenha = document.getElementById('form-senha')
  const senhaAtualInput = document.getElementById('senha_atual')
  const novaSenhaInput = document.getElementById('nova_senha')
  const confirmacaoInput = document.getElementById('nova_senha_confirmation')

  const erroSenhaAtual = document.getElementById('erro-senha-atual')
  const erroNovaSenha = document.getElementById('erro-nova-senha')
  const erroConfirmacao = document.getElementById('erro-confirmacao')

  const token = document.querySelector('input[name="_token"]').value

  let timeout

  if (nicknameInput) {
    nicknameInput.addEventListener('input', function () {
      clearTimeout(timeout)
      const nickname = nicknameInput.value.trim()

      if (nickname === '') {
        nicknameFeedback.textContent = 'O nickname é obrigatório.'
        return
      }

      if (!/^[a-zA-Z0-9_-]+$/.test(nickname)) {
        nicknameFeedback.textContent = 'Permitido apenas letras, números, traço e underline.'
        return
      }

      timeout = setTimeout(() => {
        axios
          .get('/usuario/verificar-nickname', {
            params: { nickname }
          })
          .then(response => {
            if (!response.data.disponivel) {
              nicknameFeedback.textContent = 'Este nickname já está em uso.'
            } else {
              nicknameFeedback.textContent = ''
            }
          })
      }, 400)
    })
  }

  if (formNickname) {
    formNickname.addEventListener('submit', function (e) {
      e.preventDefault()

      const nickname = nicknameInput.value.trim()
      const webhook = webhookInput.value.trim()

      nicknameFeedback.textContent = ''
      webhookFeedback.textContent = ''

      if (nickname === '') {
        nicknameFeedback.textContent = 'O nickname é obrigatório.'
        return
      }

      if (!/^[a-zA-Z0-9_-]+$/.test(nickname)) {
        nicknameFeedback.textContent = 'Permitido apenas letras, números, traço e underline.'
        return
      }

      if (nicknameFeedback.textContent !== '') return

      axios
        .post('/usuario/alterar-perfil', {
          nickname,
          webhook
        }, {
          headers: { 'X-CSRF-TOKEN': token }
        })
        .then(() => {
          Swal.fire({
            icon: 'success',
            title: 'Dados atualizados com sucesso',
            confirmButtonText: 'OK'
          }).then(() => location.reload())
        })
    })
  }

  senhaAtualInput.addEventListener('input', () => {
    if (senhaAtualInput.value.trim() !== '') erroSenhaAtual.textContent = ''
  })

  novaSenhaInput.addEventListener('input', () => {
    const senha = novaSenhaInput.value.trim()
    const confirmacao = confirmacaoInput.value.trim()

    if (senha !== '') erroNovaSenha.textContent = ''
    if (senha.length >= 6) erroNovaSenha.textContent = ''
    if (confirmacao !== '' && senha === confirmacao) erroConfirmacao.textContent = ''
  })

  confirmacaoInput.addEventListener('input', () => {
    const senha = novaSenhaInput.value.trim()
    const confirmacao = confirmacaoInput.value.trim()
    if (confirmacao !== '') erroConfirmacao.textContent = ''
    if (senha === confirmacao) erroConfirmacao.textContent = ''
  })

  if (formSenha) {
    formSenha.addEventListener('submit', function (e) {
      e.preventDefault()

      const senhaAtual = senhaAtualInput.value.trim()
      const novaSenha = novaSenhaInput.value.trim()
      const confirmacao = confirmacaoInput.value.trim()

      erroSenhaAtual.textContent = ''
      erroNovaSenha.textContent = ''
      erroConfirmacao.textContent = ''

      let erro = false

      if (senhaAtual === '') {
        erroSenhaAtual.textContent = 'Este campo é requerido.'
        erro = true
      }

      if (novaSenha === '') {
        erroNovaSenha.textContent = 'Este campo é requerido.'
        erro = true
      } else if (novaSenha.length < 6) {
        erroNovaSenha.textContent = 'A nova senha deve ter no mínimo 6 caracteres.'
        erro = true
      }

      if (confirmacao === '') {
        erroConfirmacao.textContent = 'Este campo é requerido.'
        erro = true
      } else if (novaSenha !== confirmacao) {
        erroConfirmacao.textContent = 'As senhas não coincidem.'
        erro = true
      }

      if (erro) return

      axios
        .post('/usuario/alterar-senha', {
          senha_atual: senhaAtual,
          nova_senha: novaSenha,
          nova_senha_confirmation: confirmacao
        }, {
          headers: { 'X-CSRF-TOKEN': token }
        })
        .then(() => {
          Swal.fire({
            icon: 'success',
            title: 'Senha alterada com sucesso',
            confirmButtonText: 'OK'
          }).then(() => location.reload())
        })
        .catch(error => {
          if (
            error.response &&
            error.response.status === 403 &&
            error.response.data.erro === 'Senha atual incorreta'
          ) {
            Swal.fire({
              icon: 'error',
              title: 'Senha atual incorreta',
              confirmButtonText: 'OK'
            })
          } else {
            erroNovaSenha.textContent = 'Erro ao alterar senha.'
          }
        })
    })
  }
})
