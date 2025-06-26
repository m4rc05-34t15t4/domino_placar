document.addEventListener('DOMContentLoaded', function() {

    $JOGADORES =  {};
    $JOGADORES_ESTATISTICAS = {};
    $JOG_ESTATISTICAS_TOTAIS = {
        "partidas" : { "titulo" : "🎮 Partidas", "dados" : [[0, 0, null]], "total" : 0 },
        "vitorias" : { "titulo" : "🏆 Vitórias", "dados" : [[0, 0, null]], "total" : 0 },
        "derrotas" : { "titulo" : "💀 Derrotas", "dados" : [[0, 0, null]], "total" : 0 },
        "placar_vitoria" : { "titulo" : "⚽ Placar Vitória", "dados" : [[0, 0, null]], "total" : 0 },
        "placar_derrota" : { "titulo" : "😞 Placar Derrota", "dados" : [[0, 0, null]], "total" : 0 },
        "empates" : { "titulo" : "🤝 Empates", "dados" : [[0, 0, null]], "total" : 0 },
        "merda" : { "titulo" : "💩 Merdas", "dados" : [[0, 0, null]], "total" : 0 },
        "merito" : { "titulo" : "🎯 Méritos", "dados" : [[0, 0, null]], "total" : 0 },
        "laelo" : { "titulo" : "🔀 Lá e Lô", "dados" : [[0, 0, null]], "total" : 0 },
        "cruzada" : { "titulo" : "⚔️ Cruzada", "dados" : [[0, 0, null]], "total" : 0 },
        "pontos" : { "titulo" : "📊 Pontos", "dados" : [[0, 0, null]], "total" : 0 }
    };
    $DUPLAS_ESTATISTICAS = {};
    $DUPLAS_ESTATISTICAS_TOTAIS = {};
    $PARTIDAS = [];

    const ModalPartida = new bootstrap.Modal(document.getElementById('ModalPartida'));

    async function buscarDados(opcao='ALL') {
        try {
            const response = await fetch('php/api.php?opcao='+opcao);
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }

            const dados = await response.json();
            console.log(dados); // ou qualquer manipulação que deseje fazer com os dados
            return dados;
        } catch (erro) {
            console.error('Erro ao buscar os dados:', erro);
            return null;
        }
    }

    async function editarJogador(idt) {
        const input = document.getElementById(`jogador-${idt}`);
        if (input.hasAttribute("readonly")) {
            input.removeAttribute("readonly");
            input.focus();
        } else {
            input.setAttribute("readonly", true);
            
            if (confirm(`Tem certeza que deseja atualizar o jogador "${$JOGADORES[idt]}"?`)) {

                // Cria um objeto com os dados do formulário
                var dados = {
                    id: idt,
                    acao: "UPDATE", 
                    nome: input.value
                };
                if(validarNome(dados.nome)){
                    try {
                        const response = await fetch("php/salvar_jogador.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(dados)
                        });

                        if (!response.ok) {
                            throw new Error("Erro ao salvar os dados");
                        }

                        const resultado = await response.json();
                        console.log(dados.acao, resultado);
                        
                        if( resultado["resultado"]["success"] && resultado["resultado"]["data"][0]["id"] ){
                            alert("Sucesso na solicitação!");
                            $JOGADORES[idt] = dados.nome;
                            renderizarListaJogadores();
                        }
                        else throw new Error("Houve algum erro!");
                    } catch (erro) {
                        console.error(erro);
                        alert("Erro ao atualizar a jogador.");
                    }
                }
            }
            const novoNome = input.value.trim();
            const jogador = $JOGADORES[idt];
            if (jogador) jogador.nome = novoNome;
        }
    }

    async function excluirJogador(idt) {
        if (confirm(`Tem certeza que deseja remover o jogador "${$JOGADORES[idt]}"?`)) {
            if (idt !== -1) {
                
                // Cria um objeto com os dados do formulário
                var dados = {
                    id: idt,
                    acao: "DELETE", 
                    nome: $JOGADORES[idt]
                };
                try {
                    const response = await fetch("php/salvar_jogador.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(dados)
                    });

                    if (!response.ok) {
                        throw new Error("Erro ao salvar os dados");
                    }

                    const resultado = await response.json();
                    console.log(dados.acao, resultado);
                    
                    if( resultado["resultado"]["success"] && resultado["resultado"]["data"][0]["id"] ){
                        alert("Sucesso na solicitação!");
                        delete $JOGADORES[idt];
                        location.reload();
                        //renderizarListaJogadores();
                    }
                    else throw new Error("Houve algum erro!");
                } catch (erro) {
                    console.error(erro);
                    alert("Erro ao salvar a jogador.");
                }

            }
        }
    }

    function renderizarListaJogadores() {
        const container = document.getElementById("listaJogadores");
        container.innerHTML = "";
        const linha = document.createElement("div");
        linha.classList.add("input-group", "mb-2");
        linha.innerHTML = `<input type="text" class="form-control border-success" value="" id="jogador-new"><button id="bt_add_jogador_linha" class="btn btn-outline-success">Adicionar Jogador</button>`;
        container.appendChild(linha);

        Object.entries($JOGADORES).forEach(([id, nome]) => {
            const linha = document.createElement("div");
            linha.classList.add("input-group", "mb-2");

            $bt = '';
            if(administrador()) {
                $bt = `<button id="bt_editar_jogador_${id}" id_jogador="${id}" class="btn btn-outline-secondary editarJogador">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                    <path d="M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708l-9.439 9.44a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l9.44-9.44zM11.207 2L3 10.207V11h.793L13 3.793 11.207 2zM2 12v1h1l.293-.293L2 12z"/>
                    </svg>
                </button>
                <button id="bt_excluir_jogador_${id}" id_jogador="${id}" class="btn btn-outline-danger excluirJogador">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5a.5.5 0 0 1 .5-.5h.5v7h-1v-7zm3 0a.5.5 0 0 1 .5-.5h.5v7h-1v-7z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3a.5.5 0 0 0 0 1H3h10h.5a.5.5 0 0 0 0-1H13h-1.5l-1-1h-3l-1 1H3H2.5z"/>
                    </svg>
                </button>`;
            }

            linha.innerHTML = `
            <input type="text" class="form-control" value="${nome}" id="jogador-${id}" readonly>${$bt}`;

            container.appendChild(linha);

            if(administrador()){
                $(`#bt_editar_jogador_${id}`).click(function(){
                    editarJogador($(this).attr("id_jogador"));
                });

                $(`#bt_excluir_jogador_${id}`).click(function(){
                    excluirJogador($(this).attr("id_jogador"));
                });
            }
        });

        $(`#bt_add_jogador_linha`).click(function(){
            cadastrar_jogador();
        });

    }

    function popularSelectsJogadores(jogadores) {
        const idsSelects = ["selectJogador1", "selectJogador2", "selectJogador3", "selectJogador4"];
        
        idsSelects.forEach(id => {
            const select = document.getElementById(id);

            // Limpar opções existentes
            select.innerHTML = '';

            // Adicionar opção padrão
            const opcaoPadrao = document.createElement("option");
            opcaoPadrao.value = "";
            opcaoPadrao.textContent = "Selecione um jogador";
            select.appendChild(opcaoPadrao);

            // Adicionar jogadores
            jogadores.forEach(jogador => {
                const option = document.createElement("option");
                option.value = jogador.id;
                option.textContent = jogador.nome;
                select.appendChild(option);
            });
        });
    }

    function validarNome(nome) {
    // Verifica se o nome é uma string não vazia e tem pelo menos 2 caracteres
    if (typeof nome !== 'string' || nome.trim().length < 3) {
        alert("Por favor, insira um nome válido com pelo menos 2 caracteres.");
        return false;
    }
    return true;
}

    function validarDados(dados) {
        // Verifica se todos os jogadores foram selecionados (id > 0)
        const jogadores = [dados.jogador1, dados.jogador2, dados.jogador3, dados.jogador4];
        if (jogadores.some(id => !id || parseInt(id) <= 0)) {
            alert("Todos os jogadores devem ser selecionados.");
            return false;
        }

        // Verifica se todos os jogadores são diferentes
        const jogadoresUnicos = new Set(jogadores);
        if (jogadoresUnicos.size !== jogadores.length) {
            alert("Os jogadores devem ser diferentes entre si.");
            return false;
        }

        // Verifica se o placar é número >= 0
        const placar1Valido = !isNaN(dados.placar1) && parseInt(dados.placar1) >= 0;
        const placar2Valido = !isNaN(dados.placar2) && parseInt(dados.placar2) >= 0;
        if (!placar1Valido || !placar2Valido) {
            alert("Os placares devem ser números iguais ou maiores que zero.");
            return false;
        }

        // Verifica se a data/hora foi preenchida
        /*if (!dados.dataHora) {
            alert("A data e hora da partida devem ser preenchidas.");
            return false;
        }*/

        return true;
    }

    function formatarDataISO(dataISO) {
        const [ano, mes, dia] = dataISO.split("-").map(Number);
        const data = new Date(ano, mes - 1, dia); // forçando local time
        const diaFormatado = String(data.getDate()).padStart(2, '0');
        const mesFormatado = String(data.getMonth() + 1).padStart(2, '0');
        const anoFormatado = data.getFullYear();
        const dias = ["DOM", "SEG", "TER", "QUA", "QUI", "SEX", "SÁB"];
        const diaSemana = dias[data.getDay()];
        return `${diaFormatado}/${mesFormatado}/${anoFormatado} - ${diaSemana}`;
    }
    
    function verificar_vencedor(partida){
        $vencedor = "";
        switch (partida.jogadorbct) {
            case "1":
            case "2":
                $vencedor = "B";
                break;
            case "3":
            case "4":
                $vencedor = "A";
                break;
            default:
                if(partida.placar1 != partida.placar2) $vencedor = partida.placar1 > partida.placar2 ? "A" : "B";
                break;
        }
        return $vencedor;
    }

    function popula_jog_estatisticas_totais(container){
        //jogadores estatisticas totais
        for (const [key, valores] of Object.entries($JOG_ESTATISTICAS_TOTAIS)) $JOG_ESTATISTICAS_TOTAIS[key]['texto'] = valores["dados"][0][0] > 0 ? `${$JOG_ESTATISTICAS_TOTAIS[key]["dados"].map(v => `<span>${v[0]} ${v[2]}`).join('</span>')}</span>` : '--' ;
        console.log('$JOG_ESTATISTICAS_TOTAIS', $JOG_ESTATISTICAS_TOTAIS);
        const cardst = document.createElement("div");
        cardst.className = "cards-jogadores-estatisticas flex-grow-1 flex-wrap w-100 justify-content-center align-items-center";
        $divs_titulos_estatisticas = "";
        for (const [key, valores] of Object.entries($JOG_ESTATISTICAS_TOTAIS)) $divs_titulos_estatisticas += $JOG_ESTATISTICAS_TOTAIS[key]['texto'] != '--' ? `<div class="card card-totais shadow-sm m-1 p-2 d-flex flex-column justify-content-start align-items-center "><strong>${$JOG_ESTATISTICAS_TOTAIS[key]['titulo']} (${$JOG_ESTATISTICAS_TOTAIS[key]['total']})</strong>${$JOG_ESTATISTICAS_TOTAIS[key]['texto']}</div>` : '';
        cardst.innerHTML = `<div class="d-flex flex-rown flex-wrap justify-content-center align-items-stretch w-100">${$divs_titulos_estatisticas}</div>`;
        container.prepend(cardst);
    }

    function popula_duplas_estatisticas_totais(container){
        //duplas estatisticas totais
        for (const [key, valores] of Object.entries($DUPLAS_ESTATISTICAS_TOTAIS)) $DUPLAS_ESTATISTICAS_TOTAIS[key]['texto'] = valores["dados"][0][0] > 0 ? `${$DUPLAS_ESTATISTICAS_TOTAIS[key]["dados"].map(v => `<span>${v[0]} ${v[2]}`).join('</span>')}</span>` : '--' ;
        console.log('DUPLAS_ESTATISTICAS_TOTAIS', $DUPLAS_ESTATISTICAS_TOTAIS);
        const cardst_d = document.createElement("div");
        cardst_d.className = "cards-jogadores-estatisticas flex-grow-1 flex-wrap w-100 justify-content-center align-items-center";
        $divs_titulos_estatisticas = "";
        for (const [key, valores] of Object.entries($DUPLAS_ESTATISTICAS_TOTAIS)) $divs_titulos_estatisticas += $DUPLAS_ESTATISTICAS_TOTAIS[key]['texto'] != '--' ? `<div class="card card-totais shadow-sm m-1 p-2 d-flex flex-column justify-content-start align-items-center "><strong>${$JOG_ESTATISTICAS_TOTAIS[key]['titulo']} (${$DUPLAS_ESTATISTICAS_TOTAIS[key]['total']})</strong>${$DUPLAS_ESTATISTICAS_TOTAIS[key]['texto']}</div>` : '';
        cardst_d.innerHTML = `<div class="d-flex flex-rown flex-wrap justify-content-center align-items-stretch w-100">${$divs_titulos_estatisticas}</div>`;
        container.prepend(cardst_d);
    }

    function verificar_jogadores_estatisticas_totais(jog){
        for (const [key, valores] of Object.entries($JOG_ESTATISTICAS_TOTAIS)) {
            $jog_k = parseInt(jog[key]);
            if ( $jog_k > parseInt(valores["dados"][0][0]) && $jog_k > 0 ) $JOG_ESTATISTICAS_TOTAIS[key]["dados"] = [[$jog_k, jog.id, jog.nome]];
            else if( $jog_k == parseInt(valores["dados"][0][0]) && $jog_k > 0 ) $JOG_ESTATISTICAS_TOTAIS[key]["dados"].push([$jog_k, jog.id, jog.nome]);
            if( $jog_k > 0 && !["pontos", "placar_vitoria", "placar_derrota"].includes(key) ) $JOG_ESTATISTICAS_TOTAIS[key]["total"]++;
            else if( ["pontos", "placar_vitoria", "placar_derrota"].includes(key) ) $JOG_ESTATISTICAS_TOTAIS[key]["total"] += $jog_k;
        }
    }

    function verificar_duplas_estatisticas_totais(gde){
        gde.forEach(dupla => {
            for (const [key, valores] of Object.entries(dupla)) {
                if(valores.indexOf(",") < 0){
                    if($DUPLAS_ESTATISTICAS_TOTAIS[key] == undefined) $DUPLAS_ESTATISTICAS_TOTAIS[key] = { "dados" : [[-1, 0, null]], "total" : 0 };
                    $dup_k = $DUPLAS_ESTATISTICAS_TOTAIS[key]["dados"][0][0];
                    if ( parseInt(valores) > $dup_k) $DUPLAS_ESTATISTICAS_TOTAIS[key]["dados"] = [[parseInt(valores), dupla.id, dupla.nome]];
                    else if( parseInt(valores) == $dup_k ) $DUPLAS_ESTATISTICAS_TOTAIS[key]["dados"].push([parseInt(valores), dupla.id, dupla.nome]);
                    if( parseInt(valores) > 0 && !["pontos", "placar_vitoria", "placar_derrota"].includes(key) ) $DUPLAS_ESTATISTICAS_TOTAIS[key]["total"]++;
                    else if( ["pontos", "placar_vitoria", "placar_derrota"].includes(key) ) $DUPLAS_ESTATISTICAS_TOTAIS[key]["total"] += parseInt(valores);
                }
            }
        });
    }

    function calcular_pontos(jog){
        return ( 
            (
                ( parseInt(jog.vitorias) * 3 ) + 
                ( parseInt(jog.placar_vitoria) ) + 
                ( parseInt(jog.placar_derrota) ) + 
                ( parseInt(jog.merito) ) + 
                ( parseInt(jog.cruzada) * 4 ) + 
                ( parseInt(jog.laelo) * 3 )
            ) - ( 
                ( parseInt(jog.derrotas) * 3 ) + 
                ( parseInt(jog.merda ?? 0) * 3 ) + 
                ( parseInt(jog.empates) )
            )
        );
    }

    function popularCardsDuplasJogadores(){

        const container = document.getElementById("container-duplasjogadores");
        container.innerHTML = "";
        Object.entries($DUPLAS_ESTATISTICAS).forEach(([id_a, v_a]) => {
            const card = document.createElement("div");
            card.className = "cards-jogadores card shadow-sm m-2 p-2";
            $html = `
                <div class="d-flex flex-rown justify-content-between align-items-center px-1 py-0">
                    <span class="text-primary fw-bold" style="font-size: 17px;">${v_a.nome}</span> 
                    <span class="text-muted">ID: <strong>${id_a}</strong></span>
                </div>
                <div class="card" style="max-width: 400px;">
                    <table class="table tabela-compacta text-center align-middle mb-0">
                        <thead class="table-primary">
                            <tr style="font-size: 1rem;">
                            <th class="nome-coluna py-0 pt-1">Nome</th>
                            <th class="py-0">📊</th>
                            <th class="py-0">🎮</th>
                            <th class="py-0">🏆</th>
                            <th class="py-0">💀</th>
                            <th class="py-0">🔀</th>
                            <th class="py-0">⚔️</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            //Prepara e ordena por pontos decrescente
            var { nome, ...jog_v_a } = v_a;
            Object.entries(jog_v_a).forEach(([id_b, v_b]) => { $DUPLAS_ESTATISTICAS[id_a][id_b].pontos = calcular_pontos({...v_b}); });
            var { nome, ...jogadores } = $DUPLAS_ESTATISTICAS[id_a]; // Separar campo 'nome'
            const jogadoresOrdenados = Object.entries(jogadores).sort(([, a], [, b]) => parseInt(b.pontos ?? 0) - parseInt(a.pontos ?? 0)); // Ordenar por derrotas (decrescente)
            const resultadoOrdenado = Object.assign( {}, ...jogadoresOrdenados.map(([id, v]) => ({ ['j' + id]: v }))); // Recriar objeto com prefixo na chave (ex: j2, j4)
            
            Object.entries(resultadoOrdenado).forEach(([id_b, v_b]) => {
                $html += `
                    <tr>
                        <td id="jogador-dupla_${id_b}" jog="${id_a}" dados="${Object.values(v_b).join(',')}" class="linha_estatistica_dupla text-start">${v_b.nome}</td>
                        <td>${v_b.pontos}</td>
                        <td>${v_b.partidas}</td>
                        <td>${v_b.vitorias}</td>
                        <td>${v_b.derrotas}</td>
                        <td>${v_b.laelo}</td>
                        <td>${v_b.cruzada}</td>
                    </tr>`;
            });
            $html += `</tbody></table></div>`;
            card.innerHTML = $html;
            container.appendChild(card);
        });
        popula_duplas_estatisticas_totais(container);
    }

    function popularCardsJogadores(jog_estatisticas){
        
        //Prepara e ordena por pontos decrescente
        Object.entries(jog_estatisticas).forEach(([id_j, v_j]) => { jog_estatisticas[id_j].pontos = calcular_pontos({...v_j}); });
        const jogadoresOrdenados = Object.entries(jog_estatisticas).sort(([, a], [, b]) => parseInt(b.pontos ?? 0) - parseInt(a.pontos ?? 0)); // Ordenar por pontos (decrescente)
        const resultadoOrdenado = Object.assign( {}, ...jogadoresOrdenados.map(([id, v]) => ({ ['j' + String(id)]: v }))); // Recriar objeto com prefixo na chave (ex: j2, j4)
        const container = document.getElementById("container-jogadores");
        container.innerHTML = "";

        Object.entries(resultadoOrdenado).forEach(([id_j, jog]) => {
            const card = document.createElement("div");
            card.className = "cards-jogadores card shadow-sm m-2 p-2";
            //jog.pontos = calcular_pontos(jog);
            verificar_jogadores_estatisticas_totais(jog);
            $id = jog.id.replace(/^j/, '');
            card.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="user-img me-2 rounded" style="background-image: url('img/jogadores/${$id}.gif'), url('img/jogadores/${$id}.jpg'), url('img/avatar.png');"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-rown justify-content-between align-items-center px-1 py-0">
                            <span class="text-primary fw-bold" style="font-size: 17px;">${jog.nome}</span> 
                            <span class="text-muted">ID: <strong>${$id}</strong> | 📊Pts.: <strong>${jog.pontos}</strong></span>
                        </div>
                        <hr class="my-1">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🎮 Partidas: </span><strong>${jog.partidas}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🏆 Vitórias: </span><strong>${jog.vitorias}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>💀 Derrotas: </span><strong>${jog.derrotas}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>⚽ Placar Vit.: </span><strong>${jog.placar_vitoria}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>😞 Placar Der.: </span><strong>${jog.placar_derrota}</strong></div>
                            </div>
                            <div class="col-6" style="border-left: 1px solid rgba(0, 0, 0, 0.3);">
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🤝 Empates: </span><strong>${jog.empates}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>💩 Merdas: </span><strong>${jog.merda}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🎯 Méritos: </span><strong>${jog.merito}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🔀 Lá e Lô: </span><strong>${jog.laelo}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>⚔️ Cruzada: </span><strong>${jog.cruzada}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>`;
            container.appendChild(card);
        });

        popula_jog_estatisticas_totais(container);
    }

    function criarCardRanking(emoji, titulo, jogador, corClasse = "primary") {
        const card = document.createElement("div");
        card.className = `card m-2 p-3 shadow-sm border-${corClasse}`;
        card.style.width = "150px";
        card.innerHTML = `
            <div class="text-center fs-1">${emoji}</div>
            <div class="text-center fw-bold fs-4 text-${corClasse}">${jogador.valor}</div>
            <img src="${jogador.imagem}" alt="${jogador.nome}" class="rounded-circle mx-auto d-block my-2" width="70" height="70">
            <div class="text-center small text-muted">${jogador.nome} (#${jogador.id})</div>
        `;
        return card;
    }

    function popularCardsRankingSemanal(rankings) {
        const container = document.getElementById("container-rankSemanal");
        container.innerHTML = "";
    
        rankings.forEach(ranking => {
            // Adiciona separador da semana
            const dataFormatada = formatarDataISO(ranking.semana); // Ex: "10/06/2024"
            $("#container-rank-semanal").append(`
                <div class="d-flex align-items-center my-2 w-100">
                    <div class="flex-grow-1 border-top"></div>
                    <div class="px-3 text-nowrap text-muted small">
                        Semana ${ranking.semana_numero} - ${dataFormatada}
                    </div>
                    <div class="flex-grow-1 border-top"></div>
                </div>
            `);
    
            const cardsSemana = document.createElement("div");
            cardsSemana.className = "d-flex justify-content-around flex-wrap";
    
            // Criar os 3 cards da semana
            cardsSemana.appendChild(criarCardRanking("💩", "Merda", ranking.merda, "danger"));
            cardsSemana.appendChild(criarCardRanking("⭐", "Ponto", ranking.ponto, "primary"));
            cardsSemana.appendChild(criarCardRanking("🏅", "Mérito", ranking.merito, "success"));
    
            container.appendChild(cardsSemana);
        });
    }

    function popularCardsPartidas(partidas, jogadores) {
        const container = document.getElementById("container-partidas"); // certifique-se de que existe uma div com esse id
        container.innerHTML = ""; // Limpa o conteúdo anterior
        $data_dia = "";
        partidas.forEach(partida => {
            const card = document.createElement("div");
            card.className = "cards-partidas card shadow-sm m-2 p-0";
            
            //hr data
            if(String(partida.data_hora).slice(0, 10) != $data_dia) {
                $data_dia = String(partida.data_hora).slice(0, 10);
                $("#container-partidas").append(`
                    <div class="d-flex align-items-center my-1 w-100">
                        <div class="flex-grow-1 border-top"></div>
                        <div class="px-3 text-nowrap text-muted small">
                            ${formatarDataISO($data_dia)}
                        </div>
                        <div class="flex-grow-1 border-top"></div>
                    </div>
                `);
            }

            const data = new Date(partida.data_hora);
            const dataFormatada = data.toISOString().slice(0, 10).replace("T", " ");
            $merda = `<img class="img_merda_p mx-1" src="img/merda-fill.png"/>`;
            $m1 = partida.jogadorbct == 1 ? $merda : '';
            $m2 = partida.jogadorbct == 2 ? $merda : '';
            $m3 = partida.jogadorbct == 3 ? $merda : '';
            $m4 = partida.jogadorbct == 4 ? $merda : '';

            $placar = `${partida.placar1} x ${partida.placar2}`;
            $v_cencedor = verificar_vencedor(partida);
            $medalha = partida.jogadas != null ? "medalha" : "vitoria";
            if($v_cencedor == "A") $placar = `<div class="${$medalha}"></div>${$placar}<div class="derrota"></div>`;
            else if($v_cencedor == "B") $placar = `<div class="derrota"></div>${$placar}<div class="${$medalha}"></div>`;
            
            card.innerHTML = `
                <div id="div_partida_${partida.id}" dados_partida="${Object.values(partida)}" class="card-partida card-body m-0 px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col text-primary">
                            <div class="jogador dupla-a d-flex flex-rown justify-content-start align-items-center">${$m1} ${jogadores[partida.jogador1_id] || "?"}</div>
                            <hr class="mx-0 my-1 p-0">
                            <div class="jogador dupla-a d-flex flex-rown justify-content-start align-items-center">${$m2} ${jogadores[partida.jogador2_id] || "?"}</div>
                        </div>
                        <div class="col d-flex flex-column justify-content-center align-items-center">
                            <small class="text-muted">${dataFormatada} (${partida.id})</small>
                            <div class="fw-bold fs-4 d-flex flex-rown justify-content-center align-items-center">${$placar}</div>
                        </div>
                        <div class="col text-danger text-end">
                            <div class="jogador dupla-b d-flex flex-rown justify-content-end align-items-center">${jogadores[partida.jogador3_id] || "?"} ${$m3}</div>
                            <hr class="mx-0 my-1 p-0">
                            <div class="jogador dupla-b d-flex flex-rown justify-content-end align-items-center">${jogadores[partida.jogador4_id] || "?"} ${$m4}</div>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(card);

            $(`#div_partida_${partida.id}`).click(function(){
                preencherFormularioPartida($(this).attr("dados_partida"));
                ModalPartida.show();
            });
        });
    }

    function getVencedoresPrimeirasDuplas(partidas) {
        // Pega somente as partidas nos índices 0 e 1 (se existirem)
        const ultimasDuas = [partidas[0], partidas[1]].filter(Boolean);
        const vencedores = ultimasDuas.map(partida => {
            if (partida.placar1 > partida.placar2) {
            return {
                partidaId: partida.id,
                vencedores: [partida.jogador1_id, partida.jogador2_id]
            };
            } else if (partida.placar2 > partida.placar1) {
            return {
                partidaId: partida.id,
                vencedores: [partida.jogador3_id, partida.jogador4_id]
            };
            } else {
                return {
                    partidaId: partida.id,
                    vencedores: null // empate ou sem resultado
                };
            }
        });
        return vencedores;
    }


    async function deletar_partida($id){
        const dados = {
            id: $id,
            acao: 'DELETE', 
            //dataHora: document.getElementById("dataHora").value,
            jogadas: getJogadasSelecionadas().join(","),
            jogadorbct: get_jogador_buceta_partida(), 
            jogador1: document.getElementById("selectJogador1").value,
            jogador2: document.getElementById("selectJogador2").value,
            jogador3: document.getElementById("selectJogador3").value,
            jogador4: document.getElementById("selectJogador4").value,
            placar1: document.getElementById("placar1").value,
            placar2: document.getElementById("placar2").value
        };
        try{
            const response = await fetch("php/salvar_partida.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(dados)
            });

            if (!response.ok) {
                throw new Error("Erro ao deletar os dados");
            }

            const resultado = await response.text();
            alert("Partida deletada com sucesso!");
            location.reload();

        } catch (erro) {
            console.error(erro);
            alert("Erro ao salvar a partida.");
        }
    }

    async function cadastrar_jogador(){
        
        // Cria um objeto com os dados do formulário
        var dados = {
            id: "new",
            acao: "INSERT", 
            nome: document.getElementById("jogador-new").value
        };
        if(dados['id'] != 'new' ) dados['acao'] = 'UPDATE';
        try {
            if(validarNome(dados['nome'])){
                const response = await fetch("php/salvar_jogador.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(dados)
                });

                if (!response.ok) {
                    throw new Error("Erro ao salvar os dados");
                }

                const resultado = await response.json();
                console.log(dados.acao, resultado);
                
                if( resultado["resultado"]["success"] && resultado["resultado"]["data"][0]["id"] ){
                    $JOGADORES[resultado["resultado"]["data"][0]["id"]] = dados.nome;
                    alert("Sucesso na solicitação!");
                    //renderizarListaJogadores();
                    location.reload();
                }
                else throw new Error("Houve algum erro!");
                
            }
        } catch (erro) {
            console.error(erro);
            alert("Erro ao salvar a jogador.");
        }
        
    }

    async function cadastrar_partida(){

        //$select_jbct = get_jogador_buceta_partida();
        //$jbct = $select_jbct > 0 ? $(`#selectJogador${$select_jbct}`).val() : 0;
        
        // Cria um objeto com os dados do formulário
        var dados = {
            id: $("#bt_submit").attr("id_partida"),
            acao: "INSERT", 
            //dataHora: document.getElementById("dataHora").value,
            jogadas: getJogadasSelecionadas().join(';'),
            jogadorbct: get_jogador_buceta_partida(), 
            jogador1: document.getElementById("selectJogador1").value,
            jogador2: document.getElementById("selectJogador2").value,
            jogador3: document.getElementById("selectJogador3").value,
            jogador4: document.getElementById("selectJogador4").value,
            placar1: document.getElementById("placar1").value,
            placar2: document.getElementById("placar2").value
        };
        if(parseInt(dados['id']) > 0) dados['acao'] = 'UPDATE';
        try {
            if( ( (dados['acao'] == 'UPDATE' && administrador() ) || dados['acao'] != 'UPDATE' ) && validarDados(dados) ){
                const response = await fetch("php/salvar_partida.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(dados)
                });

                if (!response.ok) {
                    throw new Error("Erro ao salvar os dados");
                }

                const resultado = await response.text();
                console.log(dados.acao, resultado);
                alert("Sucesso na solicitação!");
                location.reload();
                // Você pode limpar o formulário ou fechar o modal aqui
                // document.getElementById("formPartida").reset();
            }
        } catch (erro) {
            console.error(erro);
            alert("Erro ao salvar a partida.");
        }
    }

    function administrador() {
        return window.location.hash === "#adm";
    }

    function aplicarSelecaoJogadas(texto) {

        if(texto != null && texto != "" && texto != undefined){
            // Converte a string em array, removendo espaços
            const jogadasSelecionadas = String(texto).split(';').map(j => j.trim());
        
            document.querySelectorAll('.btn-check').forEach(input => {
                const label = document.querySelector(`label[for="${input.id}"]`);
                const jogada = input.getAttribute('jogada');

                if (jogadasSelecionadas.includes(jogada)) {
                    input.checked = true;
                    label.classList.add('btn-gold');
                } else {
                    input.checked = false;
                    label.classList.remove('btn-gold');
                }
            });
        }
      }

    function PrazoEdicao(dataStr) {
        const dataInformada = new Date(dataStr);
        const agora = new Date();
    
        // Diferença em milissegundos
        const diffMs = Math.abs(agora - dataInformada);
        
        // 1 dia em milissegundos
        const umDiaMs = 24 * 60 * 60 * 1000;
    
        return diffMs <= umDiaMs;
    }

    function prazo_time(dataTexto, prazo_min = 10) {
        // Converte o texto para um objeto Date
        const data = new Date(dataTexto.replace(" ", "T"));
        const agora = new Date();
        const diferencaMs = Math.abs(agora - data);
        const MinutosMs = prazo_min * 60 * 1000;
        return diferencaMs <= MinutosMs;
    }
      

    function preencherFormularioPartida(valores) {

        const campos = ["id", "data_hora", "jogador1_id", "jogador2_id", "jogador3_id", "jogador4_id", "placar1", "placar2", "jogadorbct", "jogadas"];

        // Transforma a string em array (suportando aspas simples)
        const v = valores.split(',').map(v => {
            v = v.trim();
            if(v.indexOf(":") > 0) return v;
            if (v.startsWith("'") && v.endsWith("'")) {
                return v.slice(1, -1); // remove aspas
            }
            return v;
        });

        // Cria um objeto com os pares campo:valor
        const d = {};
        campos.forEach((campo, i) => {
            d[campo] = v[i];
        });

        // Preencher data e hora
        //const now = new Date(d.data_hora+"".replace(' ', 'T').slice(0, 16));
        //const localDateTime = now.toISOString().slice(0, 16); // yyyy-MM-ddTHH:mm
        //document.getElementById('dataHora').value = localDateTime;

        // Preencher selects
        document.getElementById("selectJogador1").value = d.jogador1_id;
        document.getElementById("selectJogador2").value = d.jogador2_id;
        document.getElementById("selectJogador3").value = d.jogador3_id;
        document.getElementById("selectJogador4").value = d.jogador4_id;

        // Preencher placares
        document.getElementById("placar1").value = d.placar1;
        document.getElementById("placar2").value = d.placar2;

        // Preencher merda
        if(d.jogadorbct != null && d.jogadorbct > 0){
            $('.img_merda').attr('src', 'img/merda.png');
            $(`#merda_jogador_${d.jogadorbct}`).attr('src', 'img/merda-fill.png');
        }

        //Preencher jogadas
        aplicarSelecaoJogadas(d.jogadas);

        // Preencher id
        $("#bt_submit").attr("id_partida", d.id);
        $("#bt_submit").html("Salvar");
        $("#ModalPartida_titulo").html(`(${d.id}) ${d.data_hora.slice(0, 16)}`);
        
        if( administrador() && PrazoEdicao(d.data_hora) ) {
            $("#bt-close-partida").fadeOut(0);
            $("#bt_excluir_partida").attr("id_partida", d.id);
            if(prazo_time(d.data_hora)) $("#bt_excluir_partida").fadeIn(0);
        }
    }

    function get_jogador_buceta_partida(){
        $id_jbct = $('.img_merda[src="img/merda-fill.png"]')[0];
        if($id_jbct) $id_jbct = parseInt($id_jbct.getAttribute("id").split("_")[2]);
        else  $id_jbct = 0;
        console.log($id_jbct);
        return $id_jbct;
    }

    function getJogadasSelecionadas() {
        const selecionados = [];
        document.querySelectorAll('.btn-check:checked').forEach(input => {
          selecionados.push(input.getAttribute('jogada'));
        });
        return selecionados;
    }

    function prepara_dupla_estatisticas(dupla_estatistica){
        dupla_estatistica.forEach(dupla => {
            const [id_a, id_b] = String(dupla.id).split(",");
            const [nome_a, nome_b] = String(dupla.nome).split(",");
            [[id_a, nome_a, id_b, nome_b], [id_b, nome_b, id_a, nome_a]].forEach(d => {
                if( $DUPLAS_ESTATISTICAS[d[0]] == undefined ) $DUPLAS_ESTATISTICAS[d[0]] = {"nome" : d[1]};
                $DUPLAS_ESTATISTICAS[d[0]][d[2]] = { ...dupla }
                $DUPLAS_ESTATISTICAS[d[0]][d[2]]["id"] = d[2];
                $DUPLAS_ESTATISTICAS[d[0]][d[2]]["nome"] = d[3];
            });
        });
        console.log('DUPLAS_ESTATISTICAS', $DUPLAS_ESTATISTICAS);
    }

    //EXECUÇÃO

    $("#bt_excluir_partida").fadeOut(0);

    buscarDados().then(dados => {
        if (dados && dados.data && dados.data.get_jogadores) {
            // Cria um mapa de ID para nome do jogador
            dados.data.get_jogadores.forEach(j => {
                $JOGADORES[j.id] = j.nome;
            });
            popularSelectsJogadores(dados.data.get_jogadores);
        }
        if (dados && dados.data && dados.data.get_partidas) {
            $PARTIDAS = dados.data.get_partidas;
            popularCardsPartidas($PARTIDAS, $JOGADORES);
            $('#container-partidas').removeClass('d-none').addClass('d-flex fade-in');
        }
        if (dados && dados.data && dados.data.get_jogadores_estatistica) {
            $JOGADORES_ESTATISTICAS = dados.data.get_jogadores_estatistica;
            popularCardsJogadores($JOGADORES_ESTATISTICAS);
        }
        if (dados && dados.data && dados.data.get_duplas_estatistica) {
            verificar_duplas_estatisticas_totais(dados.data.get_duplas_estatistica);
            prepara_dupla_estatisticas(dados.data.get_duplas_estatistica);
            popularCardsDuplasJogadores();
        }
    });

    function sumir_conteudo_div(div_aparecer){
        $('#container-partidas, #container-jogadores, #container-duplasjogadores').removeClass('d-flex').addClass('d-none fade-in');
        $(div_aparecer).removeClass('d-none').addClass('d-flex fade-in');
    }

    
    //EVENTOS

    // adiciona classe dourada nos selecionados
    document.querySelectorAll('.btn-check').forEach(input => {
        input.addEventListener('change', () => {
            document.querySelectorAll('.btn-check').forEach(i => {
                const label = document.querySelector(`label[for="${i.id}"]`);
                if (i.checked) label.classList.add('btn-gold');
                else label.classList.remove('btn-gold');
            });
        });
    });

    $('.img_merda').click(function(){
        if ($(this).attr('src').includes('merda-fill.png')) $(this).attr('src', 'img/merda.png');
        else {
            $('.img_merda').attr('src', 'img/merda.png');
            $(this).attr('src', 'img/merda-fill.png');
        }
    });

    document.getElementById("formPartida").addEventListener("submit", async function(e) {
        e.preventDefault(); // Impede o envio tradicional
        cadastrar_partida();
    });

    $("#ver_estatisticas").click(function(){
        sumir_conteudo_div('#container-jogadores');
    });

    $("#ver_estatisticas_duplas").click(function(){
        sumir_conteudo_div('#container-duplasjogadores');
    });

    $("#ver_partidas").click(function(){
        sumir_conteudo_div('#container-partidas');
    });

    $("#add_jogadores").click(function(){
        renderizarListaJogadores();
        const modal = new bootstrap.Modal(document.getElementById('modalJogadores'));
        modal.show();
    });     

    $("#add_partida").click(function(){

        //const now = new Date();
        //const localDateTime = now.toISOString().slice(0, 16); // yyyy-MM-ddTHH:mm
        //document.getElementById('dataHora').value = localDateTime;

        // Preencher id
        $("#bt_submit").attr("id_partida", "0");
        $("#bt_submit").html("Cadastrar");
        $("#ModalPartida_titulo").html("Nova Partida");
        $("#bt-close-partida").fadeIn(0);
        $("#bt_excluir_partida").fadeOut(0);

        //adicionar jogadores recentes
        $vencedores = getVencedoresPrimeirasDuplas($PARTIDAS);
        $("#selectJogador1").val($vencedores[0] && $vencedores[0]['vencedores'] && $vencedores[0]['vencedores'][0] ? $vencedores[0]['vencedores'][0] : 0);
        $("#selectJogador2").val($vencedores[0] && $vencedores[0]['vencedores'] && $vencedores[0]['vencedores'][1] ? $vencedores[0]['vencedores'][1] : 0);
        $("#selectJogador3").val($vencedores[1] && $vencedores[1]['vencedores'] && $vencedores[1]['vencedores'][0] ? $vencedores[1]['vencedores'][0] : 0);
        $("#selectJogador4").val($vencedores[1] && $vencedores[1]['vencedores'] && $vencedores[1]['vencedores'][1] ? $vencedores[1]['vencedores'][1] : 0);
        $("#placar1, #placar2").val('');

        //BT jogadas
        document.querySelectorAll('.btn-check').forEach(input => {
            input.checked = false;
            const label = document.querySelector(`label[for="${input.id}"]`);
            label.classList.remove('btn-gold');
        });

        ModalPartida.show();
    });

    $("#bt_excluir_partida").click(function(){
        if(administrador()) {
            $id = $(this).attr("id_partida");
            if (confirm(`Tem certeza que deseja remover partida de ID: ${$id} ?`)){
                deletar_partida($id);
            }
        }
    });
    
});
