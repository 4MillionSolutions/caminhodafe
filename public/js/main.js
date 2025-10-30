
$(function ($) {

    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'highlight',
            onEnd: function (evt) {
                const card = evt.item;
                const from = evt.from.id;
                const to = evt.to.id;
                const opCode = card.querySelector('strong').innerText;

                console.log(`Movido ${opCode} de "${from}" para "${to}"`);

                // Exemplo: fazer requisição AJAX para atualizar status no backend
                /*
                fetch('/atualizar-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        codigo: opCode,
                        origem: from,
                        destino: to
                    })
                });
                */
            }
        });
    });



    // Abre modal ao clicar em um card
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('click', function () {
            const op = this.dataset.op;
            const cliente = this.dataset.cliente;
            const imovel = this.dataset.imovel;
            const prioridade = this.dataset.prioridade;
            const status = this.dataset.status;
            const data = this.dataset.data;

            const conteudo = `
                <div class="row mb-2">
                    <div class="col-md-4"><strong>OP:</strong> ${op}</div>
                    <div class="col-md-4"><strong>Cliente:</strong> ${cliente}</div>
                    <div class="col-md-4"><strong>Imóvel:</strong> ${imovel}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4"><strong>Prioridade:</strong> ${prioridade}</div>
                    <div class="col-md-4"><strong>Status:</strong> ${status}</div>
                    <div class="col-md-4"><strong>Data:</strong> ${data}</div>
                </div>
            `;
            // document.getElementById('modal_body_detalhes').innerHTML = conteudo;

            const modal = new bootstrap.Modal(document.getElementById('modal_detalhes'));
            modal.show();
        });
    });
})
