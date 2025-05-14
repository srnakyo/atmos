document.addEventListener('DOMContentLoaded', verificarStatusContas)

let ultimoStatus = {}
let atualizandoStatus = false

function verificarStatusContas() {
  if (atualizandoStatus) return

  atualizandoStatus = true

  const userId = document.getElementById('user-id')?.value
  if (!userId) {
    atualizandoStatus = false
    return
  }

  fetch(`/api/contas-status/${userId}`)
    .then(res => res.json())
    .then(data => {
      const agora = new Date()
      data.forEach(conta => {
        const el = document.querySelector(`[data-email="${conta.email_nike}"]`)
        if (el) {
          const syncSpan = el.querySelector('.sync-status')
          const timeSpan = el.querySelector('.sync-time')
          const quickTaskBtn = el.querySelector('.quick-task')
          const ultimaSync = conta.last_sync ? new Date(conta.last_sync) : null

          const statusAnterior = ultimoStatus[conta.id]
          ultimoStatus[conta.id] = conta.sincronizado

          if (syncSpan) {
            if (conta.sincronizado) {
              syncSpan.innerHTML = '<span class="piscar">🟢</span> Sincronizado'
              syncSpan.className = 'text-sm font-semibold sync-status text-green-400'
            } else {
              syncSpan.innerHTML = '<span class="piscar">🔴</span> Desconectado'
              syncSpan.className = 'text-sm font-semibold sync-status text-red-400'
            }
          }

          if (quickTaskBtn) {
            if (conta.sincronizado) {
              quickTaskBtn.style.display = 'inline-flex'
            } else {
              quickTaskBtn.style.display = 'none'
            }
          }

          if (timeSpan && ultimaSync) {
            const diffSegundos = (agora - ultimaSync) / 1000
            let tempoFormatado
            if (diffSegundos < 60) {
              tempoFormatado = `Sincronizado há ${Math.floor(diffSegundos)} segundos`
            } else if (diffSegundos < 3600) {
              tempoFormatado = `Sincronizado há ${Math.floor(diffSegundos / 60)} minutos`
            } else {
              tempoFormatado = `Última sincronização: ${ultimaSync.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              })}`
            }
            timeSpan.textContent = tempoFormatado
          } else if (timeSpan) {
            timeSpan.textContent = 'Nunca sincronizado'
          }
        }
      })

      atualizandoStatus = false
    })
    .catch(error => {
      console.error('Erro ao verificar status das contas:', error)
      atualizandoStatus = false
    })
}

setInterval(verificarStatusContas, 5000)
