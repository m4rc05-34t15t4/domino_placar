<?php 

    const HORA_INICIO_PRINCIPAL = "12:00:00";
    const HORA_FIM_PRINCIPAL = "13:10:00";

    function sqlPartidaComVencedor(): string {
        return "
            select
                p.id as id,
                p.data_hora as data_hora,
                p.jogador1_id as jogador1_id,
                p.jogador2_id as jogador2_id,
                p.jogador3_id as jogador3_id,
                p.jogador4_id as jogador4_id,
                p.placar1 as placar1,
                p.placar2 as placar2,
                p.jogadorbct as jogadorbct,
                p.jogadas as jogadas,
                case
                    when p.jogadorbct is not null then
                        case
                            when p.jogadorbct in (1, 2) then 'b'
                            when p.jogadorbct in (3, 4) then 'a'
                            when p.placar1 > p.placar2 then 'a'
                            when p.placar2 > p.placar1 then 'b'
                            else 'ab'
                        end
                    else
                        case
                            when p.placar1 > p.placar2 then 'a'
                            when p.placar2 > p.placar1 then 'b'
                            else 'ab'
                        end
                end as dupla_vencedora
            from {$GLOBALS['PREFIXO_DB']}jogos.partida p
            ";
    }

    function sqlPartidaComVencedorSinuca(): string {
        return "
            select
                ps.id as id,
                ps.data_hora as data_hora,
                ps.jogador1_id as jogador1_id,
                ps.jogador2_id as jogador2_id,
                ps.placar1 as placar1,
                ps.placar2 as placar2,
                ps.jogadorbct as jogadorbct,
                case
                    when ps.jogadorbct is not null then
                        case
                            when ps.jogadorbct = 1 then 'B'
                            when ps.jogadorbct = 2 then 'A'
                            when ps.placar1 > ps.placar2 then 'A'
                            when ps.placar2 > ps.placar1 then 'B'
                            else 'AB'
                        end
                    else
                        case
                            when ps.placar1 > ps.placar2 then 'A'
                            when ps.placar2 > ps.placar1 then 'B'
                            else 'AB'
                        end
                end as vencedor
            from {$GLOBALS['PREFIXO_DB']}jogos.partida_sinuca ps
        ";
    }

    function sqlDuplasEstatisticas( string $modoHorario = 'nenhum', string $order = 'ORDER BY partidas DESC'): string {
        $whereHorario = '';
        if ($modoHorario === 'dentro') $whereHorario = " and time(p.data_hora) between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        if ($modoHorario === 'fora') $whereHorario = " and time(p.data_hora) not between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        return "
            SELECT
                CASE
                    WHEN j1.nome < j2.nome THEN CONCAT(j1.id, ',', j2.id)
                    ELSE CONCAT(j2.id, ',', j1.id)
                END AS id,
                CASE
                    WHEN j1.nome < j2.nome THEN CONCAT(j1.nome, ',', j2.nome)
                    ELSE CONCAT(j2.nome, ',', j1.nome)
                END AS nome,
                COUNT(*) AS partidas,
                SUM(
                    CASE
                        WHEN ( ( (p.jogador1_id = j1.id AND p.jogador2_id = j2.id) OR (p.jogador2_id = j1.id AND p.jogador1_id = j2.id) ) AND p.dupla_vencedora = 'A' )
                        OR ( ( (p.jogador3_id = j1.id AND p.jogador4_id = j2.id) OR (p.jogador4_id = j1.id AND p.jogador3_id = j2.id) ) AND p.dupla_vencedora = 'B' )
                        THEN 1 ELSE 0
                    END
                ) AS vitorias,
                SUM(
                    CASE
                        WHEN ( ( (p.jogador1_id = j1.id AND p.jogador2_id = j2.id) OR (p.jogador2_id = j1.id AND p.jogador1_id = j2.id) ) AND p.dupla_vencedora = 'B' ) OR 
                            ( ( (p.jogador3_id = j1.id AND p.jogador4_id = j2.id) OR (p.jogador4_id = j1.id AND p.jogador3_id = j2.id) ) AND p.dupla_vencedora = 'A' ) THEN 1 
                        ELSE 0
                    END
                ) AS derrotas,
                SUM(
                    CASE
                        WHEN p.dupla_vencedora = 'AB' THEN 1 
                        ELSE 0
                    END
                ) AS empates,
                SUM(
                    CASE
                        WHEN p.jogadas LIKE '%C%' OR p.jogadas LIKE '%L%' THEN 1 
                        ELSE 0
                    END
                ) AS merito,
                SUM(
                    CASE 
                        WHEN p.jogadas LIKE '%L%' THEN 1 
                        ELSE 0
                    END
                ) AS laelo,
                SUM(
                    CASE 
                        WHEN p.jogadas LIKE '%C%' THEN 1 
                        ELSE 0
                    END
                ) AS cruzada,
                SUM(
                    CASE
                        WHEN p.dupla_vencedora = 'A' THEN p.placar1
                        WHEN p.dupla_vencedora = 'B' THEN p.placar2
                        ELSE 0
                    END
                ) AS placar_vitoria,
                SUM(
                    CASE
                        WHEN p.dupla_vencedora = 'B' THEN p.placar1
                        WHEN p.dupla_vencedora = 'A' THEN p.placar2
                        ELSE 0
                    END
                ) AS placar_derrota
            FROM (".sqlPartidaComVencedor().") p
            JOIN {$GLOBALS['PREFIXO_DB']}jogos.jogador j1
                ON j1.id IN (
                    p.jogador1_id, p.jogador2_id,
                    p.jogador3_id, p.jogador4_id
                )
            JOIN {$GLOBALS['PREFIXO_DB']}jogos.jogador j2
                ON j2.id IN (
                    p.jogador1_id, p.jogador2_id,
                    p.jogador3_id, p.jogador4_id
                )
            AND j1.id < j2.id
            WHERE 1=1
            {$whereHorario}
            GROUP BY id, nome 
            {$order};
            ";
    }

    function sqlRankingConfrontosSinuca(): string {
        return "
            with confrontos as (
                select
                    least(p.jogador1_id, p.jogador2_id) as jogador1_id,
                    greatest(p.jogador1_id, p.jogador2_id) as jogador2_id,
                    j.id as jogador_id,
                    concat(
                        least(p.jogador1_id, p.jogador2_id),
                        ',',
                        greatest(p.jogador1_id, p.jogador2_id)
                    ) as id_rival,
                    case
                        when p.jogador1_id < p.jogador2_id
                            then concat(j1.nome, ',', j2.nome)
                        else concat(j2.nome, ',', j1.nome)
                    end as nome_rival,
                    case
                        when j.id = p.jogador1_id and p.vencedor = 'A' then 1
                        when j.id = p.jogador2_id and p.vencedor = 'B' then 1
                        else 0
                    end as vitoria,
                    case
                        when p.vencedor = 'AB' then 1 else 0
                    end as empate,
                    case
                        when j.id = p.jogador1_id and p.vencedor = 'A' then p.placar1
                        when j.id = p.jogador2_id and p.vencedor = 'B' then p.placar2
                        else 0
                    end as placar_pro,
                    case
                        when j.id = p.jogador1_id and p.vencedor = 'B' then p.placar1
                        when j.id = p.jogador2_id and p.vencedor = 'A' then p.placar2
                        else 0
                    end as placar_contra,
                    case
                        when p.jogadorbct is not null
                        and (
                                (p.jogadorbct = 1 and j.id = p.jogador1_id)
                            or (p.jogadorbct = 2 and j.id = p.jogador2_id)
                        )
                        then 1 else 0
                    end as merda,
                    case
                        when (p.vencedor = 'A' and p.placar2 = 0 and j.id = p.jogador1_id)
                        or (p.vencedor = 'B' and p.placar1 = 0 and j.id = p.jogador2_id)
                        then 1 else 0
                    end as merito
                from ( ".sqlPartidaComVencedorSinuca()." ) p 
                join {$GLOBALS['PREFIXO_DB']}jogos.jogador j1 on j1.id = p.jogador1_id
                join {$GLOBALS['PREFIXO_DB']}jogos.jogador j2 on j2.id = p.jogador2_id
                join {$GLOBALS['PREFIXO_DB']}jogos.jogador j  on j.id in (p.jogador1_id, p.jogador2_id)
            ),
            agregado_por_jogador as (
                select
                    id_rival,
                    nome_rival,
                    jogador_id,
                    sum(vitoria) as vitoria,
                    sum(placar_pro) as placar_vitoria,
                    sum(placar_contra) as placar_derrota,
                    sum(merda) as merda,
                    sum(merito) as merito,
                    sum(empate) as empate
                from confrontos
                group by id_rival, nome_rival, jogador_id
            ),
            contagem_partidas as (
                select
                    concat(
                        least(jogador1_id, jogador2_id),
                        ',',
                        greatest(jogador1_id, jogador2_id)
                    ) as id_rival,
                    count(*) as partidas
                from ( ".sqlPartidaComVencedorSinuca()." ) p 
                group by
                    least(jogador1_id, jogador2_id),
                    greatest(jogador1_id, jogador2_id)
            ),
            agregado_final as (
                select
                    a.id_rival,
                    a.nome_rival,
                    group_concat(a.vitoria order by a.jogador_id separator ',') as vitorias,
                    group_concat(a.placar_vitoria order by a.jogador_id separator ',') as placar_vitoria,
                    group_concat(a.placar_derrota order by a.jogador_id separator ',') as placar_derrota,
                    group_concat(a.merda order by a.jogador_id separator ',') as merdas,
                    group_concat(a.merito order by a.jogador_id separator ',') as meritos,
                    sum(a.empate) as empates,
                    p.partidas as partidas
                from agregado_por_jogador a
                join contagem_partidas p on p.id_rival = a.id_rival
                group by a.id_rival, a.nome_rival, p.partidas
            )
            select
                id_rival,
                nome_rival,
                vitorias,
                placar_vitoria,
                placar_derrota,
                merdas,
                meritos,
                empates,
                partidas
            from agregado_final
            order by partidas desc
        ";
    }

    //RANK MENSAL
    function gerarSqlRankingMensal(string $modoHorario = 'nenhum', string $order = 'order by ano desc, mes desc'): string {
        $filtroHorario = '';
        if ($modoHorario === 'dentro') $filtroHorario = " and time(p.data_hora) between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        if ($modoHorario === 'fora') $filtroHorario = " and time(p.data_hora) not between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        return "
            with estatistica_mensal as (
                select
                    j.id as jogador_id,
                    j.nome as nome,
                    month(p.data_hora) as mes,
                    year(p.data_hora) as ano,
                    count(
                        case
                            when ( 
                                ( (j.id = p.jogador1_id or j.id = p.jogador2_id) and p.dupla_vencedora = 'a' ) or 
                                ( (j.id = p.jogador3_id or j.id = p.jogador4_id) and p.dupla_vencedora = 'b' ) ) then 1
                        end
                    ) as vitorias,
                    sum(
                        case
                            when p.jogadorbct is not null
                            and (
                                (p.jogadorbct = 1 and j.id = p.jogador1_id) or 
                                (p.jogadorbct = 2 and j.id = p.jogador2_id) or 
                                (p.jogadorbct = 3 and j.id = p.jogador3_id) or 
                                (p.jogadorbct = 4 and j.id = p.jogador4_id)
                            )
                            then 1 else 0
                        end
                    ) as merda,
                    sum(
                        case
                            when (
                                ( (j.id = p.jogador1_id or j.id = p.jogador2_id) and p.dupla_vencedora = 'a' ) or
                                ( (j.id = p.jogador3_id or j.id = p.jogador4_id) and p.dupla_vencedora = 'b' )
                            )
                            and (p.jogadas like '%c%' or p.jogadas like '%l%')
                            then 1 else 0
                        end
                    ) as merito
                from {$GLOBALS['PREFIXO_DB']}jogos.jogador j
                join (".sqlPartidaComVencedor().") p
                on j.id in (
                        p.jogador1_id,
                        p.jogador2_id,
                        p.jogador3_id,
                        p.jogador4_id
                    )
                where p.data_hora is not null
                {$filtroHorario}
                group by
                    j.id,
                    j.nome,
                    month(p.data_hora),
                    year(p.data_hora)
            ),
            ranking_por_mes as (
                select
                    em.*,
                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.merda desc, em.vitorias
                    ) as rn_merda,

                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.merito desc, em.vitorias desc
                    ) as rn_merito,

                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.vitorias desc
                    ) as rn_vitorias
                from estatistica_mensal em
            ),
            meses_com_partidas as (
                select distinct
                    month(data_hora) as mes,
                    year(data_hora) as ano
                from (".sqlPartidaComVencedor().") p
                where data_hora is not null
            ),
            meses_numerados as (
                select
                    mes,
                    ano,
                    row_number() over (order by ano, mes) as id
                from meses_com_partidas
            ),
            top_mensal as (
                select distinct
                    mn.id,
                    mn.mes,
                    mn.ano,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_merda) as id_top_merda,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_merda) as nome_top_merda,
                    first_value(rm.merda)      over (partition by rm.ano, rm.mes order by rm.rn_merda) as merda,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_merito) as id_top_merito,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_merito) as nome_top_merito,
                    first_value(rm.merito)     over (partition by rm.ano, rm.mes order by rm.rn_merito) as merito,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as id_top_vitoria,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as nome_top_vitorias,
                    first_value(rm.vitorias)   over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as vitorias
                from ranking_por_mes rm
                join meses_numerados mn
                on rm.mes = mn.mes
                and rm.ano = mn.ano
            )
            select
                id,
                mes,
                ano,
                id_top_merda,
                nome_top_merda,
                merda,
                id_top_merito,
                nome_top_merito,
                merito,
                id_top_vitoria,
                nome_top_vitorias,
                vitorias
            from top_mensal
            {$order};
        ";
    }

    function gerarSqlRankingSinucaMensal(string $modoHorario = 'nenhum', string $order = 'order by ano desc, mes desc'): string {
        $whereHorarioPartida = '';
        $whereHorarioMeses   = '';
        if ($modoHorario === 'dentro') {
            $whereHorarioPartida = " and time(p.data_hora) between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
            $whereHorarioMeses   = " where time(data_hora) between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        }
        if ($modoHorario === 'fora') {
            $whereHorarioPartida = " and time(p.data_hora) not between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
            $whereHorarioMeses   = " where time(data_hora) not between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        }
        return "
            with estatistica_mensal as (
                select
                    j.id as jogador_id,
                    j.nome as nome,
                    month(p.data_hora) as mes,
                    year(p.data_hora) as ano,
                    count(
                        case
                            when (
                                (j.id = p.jogador1_id and p.vencedor = 'a')  or
                                (j.id = p.jogador2_id and p.vencedor = 'b')
                            )
                            then 1
                        end
                    ) as vitorias,
                    sum(
                        case
                            when p.jogadorbct is not null
                            and (
                                (p.jogadorbct = 1 and j.id = p.jogador1_id)  or 
                                (p.jogadorbct = 2 and j.id = p.jogador2_id)
                            )
                            then 1 else 0
                        end
                    ) as merda,
                    sum(
                        case
                            when (
                                (j.id = p.jogador1_id and p.vencedor = 'a' and p.placar1 >= 4 and p.placar2 = 0) or 
                                (j.id = p.jogador2_id and p.vencedor = 'b' and p.placar2 >= 4 and p.placar1 = 0)
                            )
                            then 1 else 0
                        end
                    ) as merito
                from {$GLOBALS['PREFIXO_DB']}jogos.jogador j
                join (".sqlPartidaComVencedorSinuca().") p
                    on j.id in (p.jogador1_id, p.jogador2_id)
                where p.data_hora is not null
                {$whereHorarioPartida}
                group by
                    j.id,
                    j.nome,
                    month(p.data_hora),
                    year(p.data_hora)
            ),
            ranking_por_mes as (
                select
                    em.*,
                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.merda desc, em.vitorias
                    ) as rn_merda,
                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.merito desc, em.vitorias desc
                    ) as rn_merito,
                    row_number() over (
                        partition by em.ano, em.mes
                        order by em.vitorias desc
                    ) as rn_vitorias
                from estatistica_mensal em
            ),
            meses_com_partidas as (
                select distinct
                    month(data_hora) as mes,
                    year(data_hora) as ano
                from (".sqlPartidaComVencedorSinuca().") p 
                {$whereHorarioMeses}
            ),
            meses_numerados as (
                select
                    mes,
                    ano,
                    row_number() over (order by ano, mes) as id
                from meses_com_partidas
            ),
            top_mensal as (
                select distinct
                    mn.id,
                    mn.mes,
                    mn.ano,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_merda) as id_top_merda,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_merda) as nome_top_merda,
                    first_value(rm.merda)      over (partition by rm.ano, rm.mes order by rm.rn_merda) as merda,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_merito) as id_top_merito,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_merito) as nome_top_merito,
                    first_value(rm.merito)     over (partition by rm.ano, rm.mes order by rm.rn_merito) as merito,
                    first_value(rm.jogador_id) over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as id_top_vitoria,
                    first_value(rm.nome)       over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as nome_top_vitorias,
                    first_value(rm.vitorias)   over (partition by rm.ano, rm.mes order by rm.rn_vitorias) as vitorias
                from ranking_por_mes rm
                join meses_numerados mn
                on rm.mes = mn.mes
                and rm.ano = mn.ano
            )
            select
                id,
                mes,
                ano,
                id_top_merda,
                nome_top_merda,
                merda,
                id_top_merito,
                nome_top_merito,
                merito,
                id_top_vitoria,
                nome_top_vitorias,
                vitorias
            from top_mensal
            {$order};
        ";
    }

    function gerarSqlJogadorEstatistica( string $modoHorario = 'nenhum', ?int $limiteUltimasPartidas = 25, string $order = 'order by partidas desc'): string {
        // ----- filtro de horário -----
        $filtroHorario = '';
        if ($modoHorario === 'fora') $filtroHorario = " where cast(p.data_hora as time) < '".HORA_INICIO_PRINCIPAL."' or cast(p.data_hora as time) > '".HORA_FIM_PRINCIPAL."'";
        if ($modoHorario === 'dentro') $filtroHorario = " where cast(p.data_hora as time) between '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."'";
        // ----- limite por jogador -----
        $filtroLimite = '';
        if (!empty($limiteUltimasPartidas) && $limiteUltimasPartidas > 0) $filtroLimite = " where rp.rn <= {$limiteUltimasPartidas}";
        return "
            with partidas_expandidas as (
                select p.id as partida_id, p.data_hora, p.jogador1_id as jogador_id
                from (".sqlPartidaComVencedor().") p
                {$filtroHorario}
                union all
                select p.id, p.data_hora, p.jogador2_id
                from (".sqlPartidaComVencedor().") p
                {$filtroHorario}
                union all
                select p.id, p.data_hora, p.jogador3_id
                from (".sqlPartidaComVencedor().") p
                {$filtroHorario}
                union all
                select p.id, p.data_hora, p.jogador4_id
                from (".sqlPartidaComVencedor().") p
                {$filtroHorario}
            ),
            ranked_participacoes as (
                select
                    pe.jogador_id,
                    pe.partida_id,
                    pe.data_hora,
                    row_number() over (
                        partition by pe.jogador_id
                        order by pe.data_hora desc
                    ) as rn
                from partidas_expandidas pe
            ),
            partidas_filtradas as (
                select distinct jogador_id, partida_id
                from ranked_participacoes rp
                {$filtroLimite}
            ),
            qtd_partidas_utilizadas as (
                select jogador_id, count(*) as qtd_partidas_utilizadas
                from partidas_filtradas
                group by jogador_id
            ),
            partidas_jogador_filtradas as (
                select
                    p.id,
                    p.data_hora,
                    p.jogador1_id,
                    p.jogador2_id,
                    p.jogador3_id,
                    p.jogador4_id,
                    p.placar1,
                    p.placar2,
                    p.jogadorbct,
                    p.jogadas,
                    p.dupla_vencedora,
                    f.jogador_id as jogador_filtrado_id
                from (".sqlPartidaComVencedor().") p
                join partidas_filtradas f on p.id = f.partida_id
            )
            select
                j.id,
                j.nome,
                count(*) as partidas,
                q.qtd_partidas_utilizadas,
                sum(
                    case
                        when ( 
                            (j.id in (p.jogador1_id, p.jogador2_id) and p.dupla_vencedora = 'a') or 
                            (j.id in (p.jogador3_id, p.jogador4_id) and p.dupla_vencedora = 'b')
                        )
                        then 1 else 0
                    end
                ) as vitorias,
                sum(
                    case
                        when (
                            (j.id in (p.jogador1_id, p.jogador2_id) and p.dupla_vencedora = 'b') or 
                            (j.id in (p.jogador3_id, p.jogador4_id) and p.dupla_vencedora = 'a')
                        )
                        then 1 else 0
                    end
                ) as derrotas,
                sum(
                    case
                        when p.dupla_vencedora = 'ab' then 1 else 0
                    end
                ) as empates,
                sum(
                    case
                        when p.jogadorbct is not null
                        and (
                            (p.jogadorbct = 1 and j.id = p.jogador1_id) or 
                            (p.jogadorbct = 2 and j.id = p.jogador2_id) or 
                            (p.jogadorbct = 3 and j.id = p.jogador3_id) or 
                            (p.jogadorbct = 4 and j.id = p.jogador4_id)
                        )
                        then 1 else 0
                    end
                ) as merda,
                sum(
                    case
                        when (
                            ( (j.id in (p.jogador1_id, p.jogador2_id) and p.dupla_vencedora = 'a') or 
                            (j.id in (p.jogador3_id, p.jogador4_id) and p.dupla_vencedora = 'b') ) and 
                            (p.jogadas like '%C%' or p.jogadas like '%L%')
                        )
                        then 1 else 0
                    end
                ) as merito,
                sum(
                    case
                        when ( 
                            ( (j.id = p.jogador1_id or j.id = p.jogador2_id) and p.dupla_vencedora = 'a' ) or
                            ( (j.id = p.jogador3_id or j.id = p.jogador4_id) and p.dupla_vencedora = 'b' )
                        ) and lower(p.jogadas) like '%L%'
                        then 1 else 0
                    end
                ) as laelo,
                sum(
                    case
                        when (
                            ( (j.id = p.jogador1_id or j.id = p.jogador2_id) and p.dupla_vencedora = 'a' ) or 
                            ( (j.id = p.jogador3_id or j.id = p.jogador4_id) and p.dupla_vencedora = 'b' )
                        ) and lower(p.jogadas) like '%C%'
                        then 1 else 0
                    end
                ) as cruzada, 
                sum(
                    case
                        when (j.id in (p.jogador1_id, p.jogador2_id) and p.dupla_vencedora = 'a') then p.placar1
                        when (j.id in (p.jogador3_id, p.jogador4_id) and p.dupla_vencedora = 'b') then p.placar2
                        else 0
                    end
                ) as placar_vitoria,
                sum(
                    case
                        when (j.id in (p.jogador1_id, p.jogador2_id) and p.dupla_vencedora = 'b') then p.placar1
                        when (j.id in (p.jogador3_id, p.jogador4_id) and p.dupla_vencedora = 'a') then p.placar2
                        else 0
                    end
                ) as placar_derrota
            from {$GLOBALS['PREFIXO_DB']}jogos.jogador j
            left join partidas_jogador_filtradas p
                on j.id = p.jogador_filtrado_id
            left join qtd_partidas_utilizadas q
                on j.id = q.jogador_id
            group by j.id, j.nome, q.qtd_partidas_utilizadas
            {$order};
        ";
    }

    function gerarSqlJogadorEstatisticaSinuca( string $modoHorario = 'nenhum', string $order = 'ORDER BY qtd_partidas_utilizadas DESC, vitorias DESC', ?int $limitePartidas = 20 ): string {
        // filtro de horário
        $filtroHorario = '';
        if ($modoHorario === 'dentro') $filtroHorario = " AND time(v.data_hora) BETWEEN '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        elseif ($modoHorario === 'fora') $filtroHorario = " AND time(v.data_hora) NOT BETWEEN '".HORA_INICIO_PRINCIPAL."' and '".HORA_FIM_PRINCIPAL."' ";
        // limite de partidas
        $filtroLimite = '';
        if (!empty($limitePartidas) && $limitePartidas > 0) $filtroLimite = " WHERE rp.rn <= {$limitePartidas} ";
        return "
            WITH partidas_expandidas AS (
                SELECT
                    v.id AS partida_id,
                    v.data_hora,
                    v.jogador1_id AS jogador_id
                FROM (".sqlPartidaComVencedorSinuca().") v 
                WHERE 1=1 {$filtroHorario}
                UNION ALL
                SELECT
                    v.id,
                    v.data_hora,
                    v.jogador2_id
                FROM  (".sqlPartidaComVencedorSinuca().") v 
                WHERE 1=1 {$filtroHorario}
            ),
            ranked_participacoes AS (
                SELECT
                    p.jogador_id,
                    p.partida_id,
                    p.data_hora,
                    ROW_NUMBER() OVER (
                        PARTITION BY p.jogador_id
                        ORDER BY p.data_hora DESC
                    ) AS rn
                FROM partidas_expandidas p
            ),
            partidas_filtradas AS (
                SELECT
                    rp.jogador_id,
                    rp.partida_id
                FROM ranked_participacoes rp
                {$filtroLimite}
            ),
            partidas_jogador_filtradas AS (
                SELECT
                    f.jogador_id AS jogador_id_base,
                    p.id AS partida_id,
                    p.data_hora,
                    p.jogador1_id,
                    p.jogador2_id,
                    p.placar1,
                    p.placar2,
                    p.jogadorbct,
                    p.vencedor
                FROM partidas_filtradas f
                JOIN (".sqlPartidaComVencedorSinuca().") p
                    ON p.id = f.partida_id
            )
            SELECT
                j.id,
                j.nome,
                COUNT(p.partida_id) AS qtd_partidas_utilizadas,
                SUM(
                    CASE
                        WHEN j.id = p.jogador1_id AND p.vencedor = 'A' THEN 1
                        WHEN j.id = p.jogador2_id AND p.vencedor = 'B' THEN 1
                        ELSE 0
                    END
                ) AS vitorias,
                SUM(
                    CASE
                        WHEN j.id = p.jogador1_id AND p.vencedor = 'B' THEN 1
                        WHEN j.id = p.jogador2_id AND p.vencedor = 'A' THEN 1
                        ELSE 0
                    END
                ) AS derrotas,
                SUM(
                    CASE
                        WHEN j.id IN (p.jogador1_id, p.jogador2_id)
                            AND p.vencedor = 'AB'
                        THEN 1 ELSE 0
                    END
                ) AS empates,
                SUM(
                    CASE
                        WHEN p.jogadorbct IS NOT NULL AND ( (p.jogadorbct = 1 AND j.id = p.jogador1_id) OR (p.jogadorbct = 2 AND j.id = p.jogador2_id) ) THEN 1 ELSE 0
                    END
                ) AS merda,
                SUM(
                    CASE
                        WHEN p.vencedor = 'A'
                        AND p.placar2 = 0
                        AND p.placar1 >= 4
                        AND j.id = p.jogador1_id
                        THEN 1
                        WHEN p.vencedor = 'B'
                        AND p.placar1 = 0
                        AND p.placar2 >= 4
                        AND j.id = p.jogador2_id
                        THEN 1
                        ELSE 0
                    END
                ) AS merito,
                SUM(
                    CASE
                        WHEN j.id = p.jogador1_id AND p.vencedor = 'A' THEN p.placar1
                        WHEN j.id = p.jogador2_id AND p.vencedor = 'B' THEN p.placar2
                        ELSE 0
                    END
                ) AS placar_vitoria,
                SUM(
                    CASE
                        WHEN j.id = p.jogador1_id AND p.vencedor = 'B' THEN p.placar1
                        WHEN j.id = p.jogador2_id AND p.vencedor = 'A' THEN p.placar2
                        ELSE 0
                    END
                ) AS placar_derrota
            FROM {$GLOBALS['PREFIXO_DB']}jogos.jogador j
            JOIN partidas_jogador_filtradas p
                ON j.id = p.jogador_id_base
            GROUP BY j.id, j.nome 
            {$order};
        ";
    }

?>
