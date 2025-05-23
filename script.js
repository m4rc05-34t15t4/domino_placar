document.addEventListener('DOMContentLoaded', function() {

    $JOGADORES = [
        'Marcos Batista',
        'Rômulo (vovô)',
        'Raoni (B)',
        'Wellison (Zezo)',
        'Wellington',
        'Otto',
        'Arruda (Mução)',
        'Miguel',
        'Wagner',
        'Jefter'
    ];

    function popularSelectsJogadores() {
        const idsSelects = ["selectJogador1", "selectJogador2", "selectJogador3", "selectJogador4"];
        idsSelects.forEach(id => {
            const select = document.getElementById(id);

            // Limpar todas as opções existentes
            select.innerHTML = '';

            // Adicionar opção padrão
            const opcaoPadrao = document.createElement("option");
            opcaoPadrao.value = "";
            opcaoPadrao.textContent = "Selecione um jogador";
            select.appendChild(opcaoPadrao);

            // Adicionar jogadores
            $JOGADORES.forEach(jogador => {
                const option = document.createElement("option");
                option.value = jogador;
                option.textContent = jogador;
                select.appendChild(option);
            });
        });
    }

    // Chamada da função ao carregar a página (ou quando quiser repopular)
    popularSelectsJogadores();


    $("#add_partida").click(function(){

        const now = new Date();
        const localDateTime = now.toISOString().slice(0, 16); // yyyy-MM-ddTHH:mm
        document.getElementById('dataHora').value = localDateTime;

        const modal = new bootstrap.Modal(document.getElementById('ModalPartida'));
        modal.show();
    });
    
});
