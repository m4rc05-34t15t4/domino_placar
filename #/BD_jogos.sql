--
-- PostgreSQL database dump
--

-- Dumped from database version 15.12
-- Dumped by pg_dump version 17.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: jogador; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jogador (
    id integer NOT NULL,
    nome character varying(100) NOT NULL
);


ALTER TABLE public.jogador OWNER TO postgres;

--
-- Name: jogador_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jogador_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jogador_id_seq OWNER TO postgres;

--
-- Name: jogador_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jogador_id_seq OWNED BY public.jogador.id;


--
-- Name: partida; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.partida (
    id integer NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    jogador1_id integer NOT NULL,
    jogador2_id integer NOT NULL,
    jogador3_id integer NOT NULL,
    jogador4_id integer NOT NULL,
    placar1 integer NOT NULL,
    placar2 integer NOT NULL,
    CONSTRAINT partida_placar1_check CHECK (((placar1 >= 0) AND (placar1 <= 6))),
    CONSTRAINT partida_placar2_check CHECK (((placar2 >= 0) AND (placar2 <= 6)))
);


ALTER TABLE public.partida OWNER TO postgres;

--
-- Name: partida_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.partida_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.partida_id_seq OWNER TO postgres;

--
-- Name: partida_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.partida_id_seq OWNED BY public.partida.id;


--
-- Name: jogador id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jogador ALTER COLUMN id SET DEFAULT nextval('public.jogador_id_seq'::regclass);


--
-- Name: partida id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida ALTER COLUMN id SET DEFAULT nextval('public.partida_id_seq'::regclass);


--
-- Data for Name: jogador; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jogador (id, nome) FROM stdin;
1	Marcos Batista
2	Rômulo (vovô)
3	Raoni (B)
4	Wellison (Zezo)
5	Wellington
7	Arruda (Mução)
8	Miguel
9	Wagner
10	Jefter
6	Otto
\.


--
-- Data for Name: partida; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.partida (id, data_hora, jogador1_id, jogador2_id, jogador3_id, jogador4_id, placar1, placar2) FROM stdin;
1	2025-05-20 18:00:00	1	2	3	4	6	3
5	2025-05-24 21:00:00	9	10	2	3	2	6
7	2025-05-23 23:16:00	1	4	10	9	1	3
2	2025-05-22 01:30:00	5	6	7	8	2	2
3	2025-05-22 23:00:00	2	4	6	8	4	4
8	2025-05-23 23:24:00	4	5	9	1	2	4
\.


--
-- Name: jogador_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jogador_id_seq', 17, true);


--
-- Name: partida_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.partida_id_seq', 8, true);


--
-- Name: jogador jogador_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jogador
    ADD CONSTRAINT jogador_pkey PRIMARY KEY (id);


--
-- Name: partida partida_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT partida_pkey PRIMARY KEY (id);


--
-- Name: partida fk_partida_jogador1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT fk_partida_jogador1 FOREIGN KEY (jogador1_id) REFERENCES public.jogador(id);


--
-- Name: partida fk_partida_jogador2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT fk_partida_jogador2 FOREIGN KEY (jogador2_id) REFERENCES public.jogador(id);


--
-- Name: partida fk_partida_jogador3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT fk_partida_jogador3 FOREIGN KEY (jogador3_id) REFERENCES public.jogador(id);


--
-- Name: partida fk_partida_jogador4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT fk_partida_jogador4 FOREIGN KEY (jogador4_id) REFERENCES public.jogador(id);


--
-- Name: partida partida_jogador1_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT partida_jogador1_id_fkey FOREIGN KEY (jogador1_id) REFERENCES public.jogador(id);


--
-- Name: partida partida_jogador2_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT partida_jogador2_id_fkey FOREIGN KEY (jogador2_id) REFERENCES public.jogador(id);


--
-- Name: partida partida_jogador3_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT partida_jogador3_id_fkey FOREIGN KEY (jogador3_id) REFERENCES public.jogador(id);


--
-- Name: partida partida_jogador4_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partida
    ADD CONSTRAINT partida_jogador4_id_fkey FOREIGN KEY (jogador4_id) REFERENCES public.jogador(id);


--
-- PostgreSQL database dump complete
--

