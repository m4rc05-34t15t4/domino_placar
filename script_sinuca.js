document.addEventListener('DOMContentLoaded', function() {

    //$JOGADORES = {};
    $JOGADORES_ESTATISTICAS = {};
    $JOGADORES_ESTATISTICAS_EXPEDIENTE = {};
    $JOGADORES_ESTATISTICAS_FORA_EXPEDIENTE = {};
    $JOG_ESTATISTICAS_TOTAIS = {
        /*"partidas" : { "titulo" : "🎮 Partidas", "dados" : [[0, 0, null]], "total" : 0 },*/
        "vitorias" : { "titulo" : "🏆 Vitórias", "dados" : [[0, 0, null]], "total" : 0 },
        "derrotas" : { "titulo" : "💀 Derrotas", "dados" : [[0, 0, null]], "total" : 0 },
        "placar_vitoria" : { "titulo" : "⚽ Placar Vitória", "dados" : [[0, 0, null]], "total" : 0 },
        "placar_derrota" : { "titulo" : "😞 Placar Derrota", "dados" : [[0, 0, null]], "total" : 0 },
        "empates" : { "titulo" : "🤝 Empates", "dados" : [[0, 0, null]], "total" : 0 },
        "merda" : { "titulo" : "💩 Merdas", "dados" : [[0, 0, null]], "total" : 0 },
        "merito" : { "titulo" : "🎯 Méritos", "dados" : [[0, 0, null]], "total" : 0 },
        "pontos" : { "titulo" : "📊 Pontos", "dados" : [[0, 0, null]], "total" : 0 }
    };
    $RIVAIS_ESTATISTICAS = {};
    $RIVAIS_ESTATISTICAS_TOTAIS = {};
    $PARTIDAS = [];

    const ModalPartida = new bootstrap.Modal(document.getElementById('ModalPartida'));

    async function buscarDados(opcao='SINUCA') {
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

    function validarDados(dados) {
        // Verifica se todos os jogadores foram selecionados (id > 0)
        const jogadores = [dados.jogador1, dados.jogador2];
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

        return true;
    }
    
    function verificar_vencedor(partida){
        $vencedor = "";
        switch (partida.jogadorbct) {
            case "1":
                $vencedor = "B";
                break;
            case "2":
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
        for (const [key, valores] of Object.entries($JOG_ESTATISTICAS_TOTAIS)) $divs_titulos_estatisticas += ($JOG_ESTATISTICAS_TOTAIS[key]['titulo'] != undefined && $JOG_ESTATISTICAS_TOTAIS[key]['texto'] != '--') ? `<div class="card card-totais shadow-sm m-1 p-2 d-flex flex-column justify-content-start align-items-center "><strong>${$JOG_ESTATISTICAS_TOTAIS[key]['titulo']} (${$JOG_ESTATISTICAS_TOTAIS[key]['total']})</strong>${$JOG_ESTATISTICAS_TOTAIS[key]['texto']}</div>` : '';
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
                    if($RIVAIS_ESTATISTICAS_TOTAIS[key] == undefined) $RIVAIS_ESTATISTICAS_TOTAIS[key] = { "dados" : [[-1, 0, null]], "total" : 0 };
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
                ( parseInt(jog.merito) * 4 ) 
            ) - ( 
                ( parseInt(jog.derrotas) * 3 ) + 
                ( parseInt(jog.merda ?? 0) * 3 ) + 
                ( parseInt(jog.empates) )
            )
        );
    }

    function separa_dados_rivais(v_b, $indice){
        $indice_rival = $indice == 0 ? 1 : 0;
        $part = parseInt(v_b.partidas);
        $vit = String(v_b.vitorias).split(",");
        $pla_vit = String(v_b.placar_vitoria).split(",");
        $pla_derr = String(v_b.placar_derrota).split(",");
        $meri = String(v_b.meritos).split(",");
        $merd = String(v_b.merdas).split(",");
        $emp = parseInt(v_b.empates);
        $pont = String(v_b.pontos).indexOf(",") > 0 ? String(v_b.pontos).split(",") : [];
        $s = $part - $emp;
        $derr = [$s - parseInt($vit[0]), $s - parseInt($vit[1])];
        $v_b1 = {"vitorias" : $vit[$indice], "placar_vitoria" : $pla_vit[$indice], "placar_derrota" : $pla_derr[$indice], "merito" : $meri[$indice], "derrotas" : $derr[$indice], "merda" : $merd[$indice], "empates" : $emp};
        $v_b2 = {"vitorias" : $vit[$indice_rival], "placar_vitoria" : $pla_vit[$indice_rival], "placar_derrota" : $pla_derr[$indice_rival], "merito" : $meri[$indice_rival], "derrotas" : $derr[$indice_rival], "merda" : $merd[$indice_rival], "empates" : $emp};
        $v_b1["pontos"] = $pont.length > 0 ? $pont[$indice] : calcular_pontos($v_b1);
        $v_b2["pontos"] = $pont.length > 0 ? $pont[$indice_rival] : calcular_pontos($v_b2);
        return { $v_b1, $v_b2 };
    }

    function popularCardsRivaisJogadores(){

        const container = document.getElementById("container-rivaisjogadores");
        container.innerHTML = "";
        Object.entries($RIVAIS_ESTATISTICAS).forEach(([id_a, v_a]) => {
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
                                <th class="py-0">🎯</th>
                                <th class="py-0">💩</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            //Prepara e ordena por pontos decrescente
            var { nome, ...jog_v_a } = v_a;
            Object.entries(jog_v_a).forEach(([id_b, v_b]) => { 
                $indice = String(v_b.id_rival).split(",").indexOf(id_a);
                var { $v_b1, $v_b2 } = separa_dados_rivais(v_b, $indice);
                $indice_rival = $indice == 0 ? 1 : 0;
                $p = [0, 0];
                $p[$indice] = $v_b1.pontos;
                $p[$indice_rival] = $v_b2.pontos;
                $RIVAIS_ESTATISTICAS[id_a][id_b].pontos = $p.join(",");
            });
            var { nome,  ...jogadores } = $RIVAIS_ESTATISTICAS[id_a]; // Separar campo 'nome'
            const jogadoresOrdenados = Object.entries(jogadores).sort(([, a], [, b]) => { parseInt(b.pontos.split(",")[$indice_rival] ?? 0) - parseInt(a.pontos.split(",")[$indice_rival] ?? 0); }); // Ordenar por pontos (decrescente)
            const resultadoOrdenado = Object.assign( {}, ...jogadoresOrdenados.map(([id, v]) => ({ ['j' + id]: v }))); // Recriar objeto com prefixo na chave (ex: j2, j4)
            Object.entries(resultadoOrdenado).forEach(([id_b, v_b]) => {
                $indice = String(v_b.id_rival).split(",").indexOf(id_a);
                const {$v_b1, $v_b2} = separa_dados_rivais(v_b, $indice);
                $html += `
                    <tr>
                        <td id="jogador-dupla_${id_b}" jog="${id_a}" dados="${Object.values(v_b).join(';')}" class="linha_estatistica_dupla text-start">${v_b.nome}</td>
                        <td>${$v_b2.pontos}<span style="color: #999">/</span><b class="text-primary">${$v_b1.pontos}</b></td>
                        <td>${v_b.partidas}</td>
                        <td>${$v_b2.vitorias}<span style="color: #999">/</span><b class="text-primary">${$v_b1.vitorias}</b></td>
                        <td>${$v_b2.merito}<span style="color: #999">/</span><b class="text-primary">${$v_b1.merito}</b></td>
                        <td>${$v_b2.merda}<span style="color: #999">/</span><b class="text-primary">${$v_b1.merda}</b></td>
                    </tr>`;
            });
            $html += `</tbody></table></div>`;
            card.innerHTML = $html;
            container.appendChild(card);
        });
        //popula_duplas_estatisticas_totais(container);
    }

    function popularCardsJogadores(jog_estatisticas, $filtro="almoço"){
        
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
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🎮 Partidas: </span><strong>${jog.qtd_partidas_utilizadas}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🏆 Vitórias: </span><strong>${jog.vitorias}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>💀 Derrotas: </span><strong>${jog.derrotas}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🤝 Empates: </span><strong>${jog.empates}</strong></div>
                            </div>
                            <div class="col-6" style="border-left: 1px solid rgba(0, 0, 0, 0.3);">
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>⚽ Placar Vit.: </span><strong>${jog.placar_vitoria}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>😞 Placar Der.: </span><strong>${jog.placar_derrota}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>💩 Merdas: </span><strong>${jog.merda}</strong></div>
                                <div class="d-flex flex-rown justify-content-between align-items-center"><span>🎯 Méritos: </span><strong>${jog.merito}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>`;
            container.appendChild(card);
        });

        popula_jog_estatisticas_totais(container);

        criar_select_filtro_estatistica(container, $filtro);
    }

    function criar_select_filtro_estatistica(container, val="almoço"){
        
        // Cria o elemento <select> com Bootstrap
        const selectForm = document.createElement("select");
        selectForm.className = "form-select bg-primary text-white mx-3 my-1 text-center";
        selectForm.id = "select_filtro_estatistica";

        // Cria e adiciona manualmente as opções
        const option1 = document.createElement("option");
        option1.value = "almoço";
        option1.textContent = "Almoço";
        selectForm.appendChild(option1);

        const option2 = document.createElement("option");
        option2.value = "fora_expediente";
        option2.textContent = "Fora do Expediente";
        selectForm.appendChild(option2);

        const option3 = document.createElement("option");
        option3.value = "all";
        option3.textContent = "Geral";
        selectForm.appendChild(option3);

        container.prepend(selectForm);

        $("#select_filtro_estatistica").val(val);

        // Adiciona o evento após inserir no DOM
        selectForm.addEventListener("change", function () {
            const valor = this.value;
            switch (valor) {
                case "almoço":
                    popularCardsJogadores($JOGADORES_ESTATISTICAS_EXPEDIENTE, "almoço");
                    break;
                case "fora_expediente":
                    popularCardsJogadores($JOGADORES_ESTATISTICAS_FORA_EXPEDIENTE, "fora_expediente");
                    break;
                case "all":
                    popularCardsJogadores($JOGADORES_ESTATISTICAS, "all");
                    break;
            }
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

            $placar = `${partida.placar1} x ${partida.placar2}`;
            $v_cencedor = verificar_vencedor(partida);
            $medalha = ( ($v_cencedor == "A" && partida.placar2 == 0) || ($v_cencedor == "B" && partida.placar1 == 0) ) ? "medalha" : "vitoria";
            if($v_cencedor == "A") $placar = `<div class="${$medalha}"></div>${$placar}<div class="derrota"></div>`;
            else if($v_cencedor == "B") $placar = `<div class="derrota"></div>${$placar}<div class="${$medalha}"></div>`;
            
            card.innerHTML = `
                <div id="div_partida_${partida.id}" dados_partida="${Object.values(partida)}" class="card-partida card-body m-0 px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col text-primary">
                            <div class="jogador dupla-a d-flex flex-rown justify-content-start align-items-center">${$m1} ${jogadores[partida.jogador1_id] || "?"}</div>
                        </div>
                        <div class="col d-flex flex-column justify-content-center align-items-center">
                            <small class="text-muted">${dataFormatada} (${partida.id})</small>
                            <div class="fw-bold fs-4 d-flex flex-rown justify-content-center align-items-center">${$placar}</div>
                        </div>
                        <div class="col text-danger text-end">
                            <div class="jogador dupla-b d-flex flex-rown justify-content-end align-items-center">${jogadores[partida.jogador2_id] || "?"} ${$m2}</div>
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
                    vencedores: [partida.jogador1_id]
                };
            } else if (partida.placar2 > partida.placar1) {
                return {
                    partidaId: partida.id,
                    vencedores: [partida.jogador2_id]
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
            jogadorbct: get_jogador_buceta_partida(), 
            jogador1: document.getElementById("selectJogador1").value,
            jogador2: document.getElementById("selectJogador2").value,
            placar1: document.getElementById("placar1").value,
            placar2: document.getElementById("placar2").value
        };
        try{
            const response = await fetch("php/salvar_partida_sinuca.php", {
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

    async function cadastrar_partida(){
        
        // Cria um objeto com os dados do formulário
        var dados = {
            id: $("#bt_submit").attr("id_partida"),
            acao: "INSERT", 
            //dataHora: document.getElementById("dataHora").value,
            jogadorbct: get_jogador_buceta_partida(), 
            jogador1: document.getElementById("selectJogador1").value,
            jogador2: document.getElementById("selectJogador2").value,
            placar1: document.getElementById("placar1").value,
            placar2: document.getElementById("placar2").value
        };
        if(parseInt(dados['id']) > 0) dados['acao'] = 'UPDATE';
        try {
            if( ( (dados['acao'] == 'UPDATE' && administrador() ) || dados['acao'] != 'UPDATE' ) && validarDados(dados) ){
                const response = await fetch("php/salvar_partida_sinuca.php", {
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

    function preencherFormularioPartida(valores) {

        const campos = ["id", "data_hora", "jogador1_id", "jogador2_id", "placar1", "placar2", "jogadorbct"];

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

        // Preencher placares
        document.getElementById("placar1").value = d.placar1;
        document.getElementById("placar2").value = d.placar2;

        // Preencher merda
        if(d.jogadorbct != null && d.jogadorbct > 0){
            $('.img_merda').attr('src', 'img/merda.png');
            $(`#merda_jogador_${d.jogadorbct}`).attr('src', 'img/merda-fill.png');
        }

        // Preencher id
        $("#bt_submit").attr("id_partida", d.id);
        $("#bt_submit").html("Salvar");
        $("#ModalPartida_titulo").html(`(${d.id}) ${d.data_hora.slice(0, 16)}`);
        
        if(administrador() && PrazoEdicao(d.data_hora)) {
            $("#bt-close-partida").fadeOut(0);
            $("#bt_excluir_partida").attr("id_partida", d.id);
            if(prazo_time(d.data_hora)) $("#bt_excluir_partida").fadeIn(0);
        }
    }

    function get_jogador_buceta_partida(){
        $id_jbct = $('.img_merda[src="img/merda-fill.png"]')[0];
        if($id_jbct) $id_jbct = parseInt($id_jbct.getAttribute("id").split("_")[2]);
        else  $id_jbct = 0;
        return $id_jbct;
    }

    function prepara_rivais_estatisticas(rivais_estatistica){
        rivais_estatistica.forEach(rivais => {
            const [id_a, id_b] = String(rivais.id_rival).split(",");
            const [nome_a, nome_b] = String(rivais.nome_rival).split(",");
            [[id_a, nome_a, id_b, nome_b], [id_b, nome_b, id_a, nome_a]].forEach(d => {
                if( $RIVAIS_ESTATISTICAS[d[0]] == undefined ) $RIVAIS_ESTATISTICAS[d[0]] = {"nome" : d[1]};
                $RIVAIS_ESTATISTICAS[d[0]][d[2]] = { ...rivais }
                $RIVAIS_ESTATISTICAS[d[0]][d[2]]["id"] = d[2];
                $RIVAIS_ESTATISTICAS[d[0]][d[2]]["nome"] = d[3];
            });
        });
        console.log('RIVAIS_ESTATISTICAS', $RIVAIS_ESTATISTICAS);
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
        if (dados && dados.data && dados.data.get_partidas_sinuca) {
            $PARTIDAS = dados.data.get_partidas_sinuca;
            popularCardsPartidas($PARTIDAS, $JOGADORES);
            $('#container-partidas').removeClass('d-none').addClass('d-flex fade-in');
        }
        if (dados && dados.data && dados.data.get_jogadores_estatistica_sinuca) {
            if(dados.data.get_jogadores_estatistica_sinuca) $JOGADORES_ESTATISTICAS = dados.data.get_jogadores_estatistica_sinuca;
            if(dados.data.get_jogadores_estatistica_sinuca_expediente) $JOGADORES_ESTATISTICAS_EXPEDIENTE = dados.data.get_jogadores_estatistica_sinuca_expediente;
            if(dados.data.get_jogadores_estatistica_sinuca_fora_expediente) $JOGADORES_ESTATISTICAS_FORA_EXPEDIENTE = dados.data.get_jogadores_estatistica_sinuca_fora_expediente;
            popularCardsJogadores($JOGADORES_ESTATISTICAS_EXPEDIENTE, "almoço");
        }
        if (dados && dados.data && dados.data.get_rivais_estatistica_sinuca) {
            //$RIVAIS_ESTATISTICAS = dados.data.get_rivais_estatistica_sinuca;
            //verificar_duplas_estatisticas_totais(dados.data.get_rivais_estatistica_sinuca);/////
            prepara_rivais_estatisticas(dados.data.get_rivais_estatistica_sinuca);
            popularCardsRivaisJogadores();
        }
        if (dados && dados.data && dados.data.get_rank_sinuca_mensal_expediente) {
            popula_rank(dados.data.get_rank_sinuca_mensal_expediente);
        }
    });

    function sumir_conteudo_div(div_aparecer){
        $('#container-partidas, #container-jogadores, #container-rivaisjogadores, #container-rank').removeClass('d-flex').addClass('d-none fade-in');
        $(div_aparecer).removeClass('d-none').addClass('d-flex fade-in');
    }

    
    //EVENTOS

    document.getElementById("formPartida").addEventListener("submit", async function(e) {
        e.preventDefault(); // Impede o envio tradicional
        cadastrar_partida();
    });

    $("#ver_estatisticas").click(function(){
        sumir_conteudo_div('#container-jogadores');
    });

    $("#ver_estatisticas_rivais").click(function(){
        sumir_conteudo_div('#container-rivaisjogadores');
    });

    $("#ver_partidas").click(function(){
        sumir_conteudo_div('#container-partidas');
    });

    $("#ver_rank").click(function(){
        sumir_conteudo_div('#container-rank');
    });

    $("#add_jogadores").click(function(){
        renderizarListaJogadores();
        const modal = new bootstrap.Modal(document.getElementById('modalJogadores'));
        modal.show();
    });     

    $("#add_partida").click(function(){

        // Preencher id
        $("#bt_submit").attr("id_partida", "0");
        $("#bt_submit").html("Cadastrar");
        $("#ModalPartida_titulo").html("Nova Partida");
        $("#bt-close-partida").fadeIn(0);
        $("#bt_excluir_partida").fadeOut(0);

        //adicionar jogadores recentes
        $vencedores = getVencedoresPrimeirasDuplas($PARTIDAS);
        $("#selectJogador1").val($vencedores[0] && $vencedores[0]['vencedores'] && $vencedores[0]['vencedores'][0] ? $vencedores[0]['vencedores'][0] : 0);
        $("#selectJogador2").val($vencedores[1] && $vencedores[1]['vencedores'] && $vencedores[1]['vencedores'][0] ? $vencedores[1]['vencedores'][0] : 0);
        $("#placar1, #placar2").val('');

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
