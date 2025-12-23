<?php $v = '?v='.time(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dominó - Placar</title>
        <link rel="stylesheet" href="styles.css?v=<?=$v;?>">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    
    <body>
        <?php require_once('php/loading.php'); ?>
        <div class="main-container container-fluid min-vh-100 bg-light p-0">
            <div class="d-flex justify-content-around align-items-center flex-wrap m-2 mt-3">
                <h1 class="h3 text-dark d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trophy text-warning" aria-hidden="true">
                        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                        <path d="M4 22h16"></path>
                        <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                        <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                        <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                    </svg> 
                    Partidas de Dominó
                </h1>
                
                <div class="d-flex gap-2 flex-wrap w-fill justify-content-center align-items-center">
                    <button id="add_partida" class="btn btn-primary d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-plus" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 12h8"></path>
                            <path d="M12 8v8"></path>
                        </svg> 
                        Add Partida
                    </button>

                    <button id="ver_partidas" class="btn btn-warning d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-list" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="6" rx="1"></rect>
                            <rect x="3" y="14" width="18" height="6" rx="1"></rect>
                        </svg>
                        Partidas
                    </button>

                    <button id="ver_rank" class="btn btn-orange d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trophy-fill " viewBox="0 0 16 16">
                            <path d="M2.5.5A.5.5 0 0 0 2 1v2a2.5 2.5 0 0 0 1.44 2.258 5.985 5.985 0 0 0 1.193 1.523A5.972 5.972 0 0 0 7.5 8.938V10H5.5a.5.5 0 0 0-.5.5v.5a3 3 0 0 0 2.5 2.958V15H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H9v-1.042A3 3 0 0 0 11.5 11v-.5a.5.5 0 0 0-.5-.5H8.5V8.938a5.972 5.972 0 0 0 2.867-1.157 5.985 5.985 0 0 0 1.193-1.523A2.5 2.5 0 0 0 14 3V1a.5.5 0 0 0-.5-.5H12a.5.5 0 0 0 0 1h1.5v2a1.5 1.5 0 0 1-1.24 1.48 5.98 5.98 0 0 0 .015-.48V1a.5.5 0 0 0-.5-.5H4.225a.5.5 0 0 0-.5.5v3.5c0 .163.005.325.015.484A1.5 1.5 0 0 1 2.5 3V1A.5.5 0 0 0 2.5.5Z"/>
                        </svg>
                        Ranking
                    </button>

                    <button id="ver_estatisticas" class="btn btn-danger d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bar-chart-2" viewBox="0 0 24 24" aria-hidden="true">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        Estatísticas
                    </button>

                    <button id="ver_estatisticas_duplas" class="btn btn-dark d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-duo-stats"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <!-- Barras de estatística -->
                            <line x1="6" y1="10" x2="6" y2="4"></line>
                            <line x1="12" y1="10" x2="12" y2="6"></line>
                            <line x1="18" y1="10" x2="18" y2="2"></line>
                            <!-- Cabeças dos usuários -->
                            <circle cx="8" cy="16" r="2"></circle>
                            <circle cx="16" cy="16" r="2"></circle>
                            <!-- Corpos dos usuários -->
                            <path d="M4 22v-2a4 4 0 0 1 4-4"></path>
                            <path d="M20 22v-2a4 4 0 0 0-4-4"></path>
                        </svg>
                        Est. Duplas
                    </button>

                    <button id="add_jogadores" class="btn btn-success d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                         Jogadores
                    </button>
                </div>
            </div>
            
            <div id="container-partidas" class="d-none div_cards-conteudo d-flex justify-content-center align-items-center flex-wrap w-fill h-fill"></div>

            <div id="container-jogadores" class="d-none div_cards-conteudo d-flex justify-content-center align-items-center flex-wrap w-fill h-fill"></div>
            
            <div id="container-duplasjogadores" class="d-none div_cards-conteudo d-flex justify-content-center align-items-start flex-wrap w-fill h-fill"></div>

            <div id="container-rank" class="d-none div_cards-conteudo d-flex justify-content-center align-items-start flex-wrap w-fill h-fill pb-3"></div>
        
        </div>

        <!-- Modal -->
        <div class="modal fade" id="ModalPartida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex flex-rown justify-content-between align-items-center bg-primary text-white">
                        <h1 class="modal-title fs-4 text-center w-100" id="ModalPartida_titulo">Nova Partida</h1>
                        <button id="bt-close-partida" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <button id="bt_excluir_partida" id_partida="0" class="btn btn-outline-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5a.5.5 0 0 1 .5-.5h.5v7h-1v-7zm3 0a.5.5 0 0 1 .5-.5h.5v7h-1v-7z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3a.5.5 0 0 0 0 1H3h10h.5a.5.5 0 0 0 0-1H13h-1.5l-1-1h-3l-1 1H3H2.5z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body mt-0 pt-0">
                        <form id="formPartida" class="mt-0 py-0">
                            <div class="modal-body">
                                <table class="text-center table table-borderless w-100 m-0">
                                    <tr>
                                        <td colspan="2">
                                            <div class="d-flex justify-content-around">
                                                <input type="checkbox" class="btn-check" id="jogadaC" autocomplete="off" jogada="C">
                                                <label class="bt-jogada btn btn-light" for="jogadaC">Cruzada</label>
                                                <input type="checkbox" class="btn-check" id="jogadaL" autocomplete="off" jogada="L">
                                                <label class="bt-jogada btn btn-light" for="jogadaL">Lá e Lô</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pb-2">
                                            <div id="div-form-dupla1">
                                                <div>
                                                    <div class="d-flex flex-rown">
                                                        <select id="selectJogador1" class="form-select text-primary border-primary text-center" required></select>
                                                        <img id="merda_jogador_1" class="img_merda m-1 ms-3" src="img/merda.png"/>
                                                    </div>
                                                    <div class="d-flex flex-rown">
                                                        <select id="selectJogador2" class="form-select text-primary border-primary text-center" required></select>
                                                        <img id="merda_jogador_2" class="img_merda m-1 ms-3" src="img/merda.png"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td rowspan="2" class="td-content-h">
                                            <div class="d-flex flex-column justify-content-around align-items-center h-100 mt-3">
                                                <input type="number" class="numero_placar form-control fs-4 border-primary text-primary" id="placar1" min="0" max="6" required>
                                                <div class="align-self-center fs-1 w-100" style="transform: translateY(-5px);">X</div>
                                                <input type="number" class="numero_placar form-control fs-4 border-danger text-danger" id="placar2" min="0" max="6" required>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-2">
                                            <div id="div-form-dupla2">
                                                <div>
                                                    <div class="d-flex flex-rown">
                                                        <select id="selectJogador3" class="form-select text-danger border-danger text-center" required></select>
                                                        <img id="merda_jogador_3" class="img_merda m-1 ms-3" src="img/merda.png"/>
                                                    </div>
                                                    <div class="d-flex flex-rown">
                                                        <select id="selectJogador4" class="form-select text-danger border-danger text-center" required></select>
                                                        <img id="merda_jogador_4" class="img_merda m-1 ms-3" src="img/merda.png"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer d-flex flex-rown justify-content-around align-items-center">
                                <button type="button" class="bt-rodape-modal btn btn-secondary fs-5" data-bs-dismiss="modal">Cancelar</button>
                                <button id="bt_submit" id_partida="0" type="submit" class="bt-rodape-modal btn btn-primary fs-5">Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="modalJogadores" tabindex="-1" aria-labelledby="modalJogadoresLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalJogadoresLabel">Lista de Jogadores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="listaJogadores">
                    <!-- Os jogadores serão adicionados dinamicamente aqui -->
                </div>
                <div class="modal-footer d-flex flex-rown justify-content-around align-items-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
                </div>
            </div>
        </div>
    </body>
    <script src="funcoes.js<?=$v;?>"></script>
    <script src="script.js<?=$v;?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</html>
