$JOGADORES = {};

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
    const idsSelects = ["selectJogador1", "selectJogador2"];
    
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

function administrador() {
    return window.location.hash === "#aaa";
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

}

//EVENTOS

$('.img_merda').click(function(){
    if ($(this).attr('src').includes('merda-fill.png')) $(this).attr('src', 'img/merda.png');
    else {
        $('.img_merda').attr('src', 'img/merda.png');
        $(this).attr('src', 'img/merda-fill.png');
    }
});
