import{S as c}from"./sweetalert2.esm.all-89IKscqO.js";import{a as h}from"./index-t--hEgTQ.js";import{I as f}from"./index-BOAzHGzj.js";window.deletarCartao=function(n){c.fire({title:"Tem certeza?",text:"Essa ação não pode ser desfeita.",icon:"warning",showCancelButton:!0,confirmButtonColor:"#78c676",cancelButtonColor:"#d33",confirmButtonText:"Sim, remover",cancelButtonText:"Cancelar"}).then(d=>{d.isConfirmed&&h.delete(`/cartoes/${n}`).then(()=>{c.fire("Removido!","O cartão foi deletado.","success").then(()=>location.reload())}).catch(()=>{c.fire("Erro","Não foi possível remover o cartão.","error")})})};document.addEventListener("DOMContentLoaded",()=>{const n=document.getElementById("modal-cartoes"),d=document.querySelector(".text-greenlight.flex"),p=document.getElementById("fechar-modal"),v=document.getElementById("add-card-form"),l=document.getElementById("form-cartoes");function i(t){fetch("/cartoes/marcas").then(o=>o.json()).then(o=>{t.innerHTML="",o.forEach(e=>{const r=document.createElement("option");r.value=e.name,r.textContent=e.name,t.appendChild(r)})})}function m(t){const o=t.querySelector('input[name="numero[]"]'),e=t.querySelector('input[name="cvv[]"]');o&&f(o,{mask:"0000 0000 0000 0000"}),e&&f(e,{mask:"000"})}function b(t){t.querySelectorAll(".erro-campo").forEach(o=>o.remove()),t.querySelectorAll("input, select").forEach(o=>{o.classList.remove("border-red-500")})}function g(t,o){t.classList.add("border-red-500");let e=document.createElement("span");e.className="erro-campo text-red-500 text-xs mt-1 block",e.textContent=o,t.insertAdjacentElement("afterend",e)}d.addEventListener("click",t=>{t.preventDefault(),n.classList.remove("hidden"),l.querySelectorAll(".form-cartao").forEach(a=>a.remove());const e=document.createElement("div");e.className="form-cartao grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 border-b border-gray-700 pb-6",e.innerHTML=`
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
            ${Array.from({length:12},(a,s)=>`<option value="${String(s+1).padStart(2,"0")}">${String(s+1).padStart(2,"0")}</option>`).join("")}
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-800 block mb-1">Ano</label>
            <select name="validade_ano[]" class="w-full bg-bg text-white p-2 rounded">
                ${Array.from({length:11},(a,s)=>{const u=new Date().getFullYear()+s;return`<option value="${u}">${u}</option>`}).join("")}
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-800 block mb-1">CVV</label>
            <input type="text" name="cvv[]" maxlength="3" class="w-20 bg-bg text-white p-2 rounded">
        </div>
        `,l.appendChild(e);const r=e.querySelector(".brand-select");r&&i(r),m(e)}),p.addEventListener("click",()=>{n.classList.add("hidden")}),n.addEventListener("click",t=>{t.target===n&&n.classList.add("hidden")}),document.addEventListener("keydown",t=>{t.key==="Escape"&&!n.classList.contains("hidden")&&n.classList.add("hidden")}),v.addEventListener("click",()=>{const o=l.querySelector(".form-cartao").cloneNode(!0);o.querySelectorAll("input, select").forEach(a=>a.value="");const e=document.createElement("button");e.type="button",e.className="text-red-500 text-sm font-semibold cursor-pointer remove-card-btn col-span-3 text-right",e.innerText="Remover",e.addEventListener("click",()=>{l.querySelectorAll(".form-cartao").length>1&&o.remove()}),o.appendChild(e),l.appendChild(o);const r=o.querySelector(".brand-select");r&&i(r),m(o)}),document.getElementById("salvar-cartoes").addEventListener("click",()=>{let t=!0;if(l.querySelectorAll(".form-cartao").forEach(r=>{b(r),r.querySelectorAll("input, select").forEach(a=>{(a.value.trim()===""||a.value==="Selecione")&&(g(a,"Campo obrigatório"),t=!1)})}),!t)return;l.querySelectorAll('input[name="numero[]"]').forEach(r=>{r.value=r.value.replace(/\s/g,"")});const e=new FormData(l);fetch("/cartoes/multiplos",{method:"POST",headers:{"X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},body:e}).then(r=>r.json()).then(r=>{r.success?c.fire({icon:"success",title:"Cartão adicionado!",text:"Cartão salvo com sucesso.",confirmButtonColor:"#78c676"}).then(()=>{location.reload()}):c.fire({icon:"error",title:"Erro",text:r.message||"Erro ao salvar cartões.",confirmButtonColor:"#e3342f"})}).catch(()=>{c.fire({icon:"error",title:"Erro",text:"Erro ao salvar cartões.",confirmButtonColor:"#e3342f"})})})});
