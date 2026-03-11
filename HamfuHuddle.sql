--
-- PostgreSQL database dump
--

\restrict gDcIosNimpeWCRIcQpGAAVaElyNN56aTaiqMafilGRoHCbZCHbgpLPP3RVuFmSj

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

-- Started on 2026-03-10 20:41:41

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
-- TOC entry 223 (class 1259 OID 24796)
-- Name: busquedas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.busquedas (
    id integer NOT NULL,
    usuario_id integer,
    tmdb_id integer NOT NULL,
    fecha_busqueda timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.busquedas OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 24795)
-- Name: busquedas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.busquedas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.busquedas_id_seq OWNER TO postgres;

--
-- TOC entry 4979 (class 0 OID 0)
-- Dependencies: 222
-- Name: busquedas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.busquedas_id_seq OWNED BY public.busquedas.id;


--
-- TOC entry 227 (class 1259 OID 24821)
-- Name: calificaciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.calificaciones (
    id integer NOT NULL,
    usuario_id integer,
    tmdb_id integer NOT NULL,
    calificacion character varying(20),
    CONSTRAINT calificaciones_calificacion_check CHECK (((calificacion)::text = ANY ((ARRAY['me_gusto'::character varying, 'neutro'::character varying, 'no_me_gusto'::character varying])::text[])))
);


ALTER TABLE public.calificaciones OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 24820)
-- Name: calificaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.calificaciones_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.calificaciones_id_seq OWNER TO postgres;

--
-- TOC entry 4980 (class 0 OID 0)
-- Dependencies: 226
-- Name: calificaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.calificaciones_id_seq OWNED BY public.calificaciones.id;


--
-- TOC entry 220 (class 1259 OID 24772)
-- Name: generos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.generos (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL
);


ALTER TABLE public.generos OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 24771)
-- Name: generos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.generos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.generos_id_seq OWNER TO postgres;

--
-- TOC entry 4981 (class 0 OID 0)
-- Dependencies: 219
-- Name: generos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.generos_id_seq OWNED BY public.generos.id;


--
-- TOC entry 218 (class 1259 OID 24758)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nombre_usuario character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    contrasena text NOT NULL,
    nombre_visible character varying(100),
    avatar character varying(100),
    fecha_creacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 24852)
-- Name: peliculas_favoritas; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.peliculas_favoritas AS
 SELECT u.nombre_usuario,
    c.tmdb_id,
    c.calificacion
   FROM (public.usuarios u
     JOIN public.calificaciones c ON ((u.id = c.usuario_id)))
  WHERE ((c.calificacion)::text = 'me_gusto'::text);


ALTER VIEW public.peliculas_favoritas OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 24809)
-- Name: recomendaciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recomendaciones (
    id integer NOT NULL,
    busqueda_id integer,
    tmdb_id integer NOT NULL,
    posicion integer
);


ALTER TABLE public.recomendaciones OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 24808)
-- Name: recomendaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recomendaciones_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recomendaciones_id_seq OWNER TO postgres;

--
-- TOC entry 4982 (class 0 OID 0)
-- Dependencies: 224
-- Name: recomendaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recomendaciones_id_seq OWNED BY public.recomendaciones.id;


--
-- TOC entry 221 (class 1259 OID 24780)
-- Name: usuario_generos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuario_generos (
    usuario_id integer NOT NULL,
    genero_id integer NOT NULL
);


ALTER TABLE public.usuario_generos OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 24757)
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- TOC entry 4983 (class 0 OID 0)
-- Dependencies: 217
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- TOC entry 229 (class 1259 OID 24834)
-- Name: vistas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vistas (
    id integer NOT NULL,
    usuario_id integer,
    tmdb_id integer NOT NULL,
    fecha_vista timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.vistas OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 24833)
-- Name: vistas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vistas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vistas_id_seq OWNER TO postgres;

--
-- TOC entry 4984 (class 0 OID 0)
-- Dependencies: 228
-- Name: vistas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vistas_id_seq OWNED BY public.vistas.id;


--
-- TOC entry 4778 (class 2604 OID 24799)
-- Name: busquedas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.busquedas ALTER COLUMN id SET DEFAULT nextval('public.busquedas_id_seq'::regclass);


--
-- TOC entry 4781 (class 2604 OID 24824)
-- Name: calificaciones id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones ALTER COLUMN id SET DEFAULT nextval('public.calificaciones_id_seq'::regclass);


--
-- TOC entry 4777 (class 2604 OID 24775)
-- Name: generos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.generos ALTER COLUMN id SET DEFAULT nextval('public.generos_id_seq'::regclass);


--
-- TOC entry 4780 (class 2604 OID 24812)
-- Name: recomendaciones id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recomendaciones ALTER COLUMN id SET DEFAULT nextval('public.recomendaciones_id_seq'::regclass);


--
-- TOC entry 4775 (class 2604 OID 24761)
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- TOC entry 4782 (class 2604 OID 24837)
-- Name: vistas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vistas ALTER COLUMN id SET DEFAULT nextval('public.vistas_id_seq'::regclass);


--
-- TOC entry 4967 (class 0 OID 24796)
-- Dependencies: 223
-- Data for Name: busquedas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.busquedas (id, usuario_id, tmdb_id, fecha_busqueda) FROM stdin;
\.


--
-- TOC entry 4971 (class 0 OID 24821)
-- Dependencies: 227
-- Data for Name: calificaciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.calificaciones (id, usuario_id, tmdb_id, calificacion) FROM stdin;
\.


--
-- TOC entry 4964 (class 0 OID 24772)
-- Dependencies: 220
-- Data for Name: generos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.generos (id, nombre) FROM stdin;
1	Action
2	Adventure
3	Animation
4	Comedy
5	Crime
6	Drama
7	Fantasy
8	Horror
9	Mystery
10	Romance
11	Sci-Fi
12	Thriller
\.


--
-- TOC entry 4969 (class 0 OID 24809)
-- Dependencies: 225
-- Data for Name: recomendaciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.recomendaciones (id, busqueda_id, tmdb_id, posicion) FROM stdin;
\.


--
-- TOC entry 4965 (class 0 OID 24780)
-- Dependencies: 221
-- Data for Name: usuario_generos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuario_generos (usuario_id, genero_id) FROM stdin;
\.


--
-- TOC entry 4962 (class 0 OID 24758)
-- Dependencies: 218
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, nombre_usuario, email, contrasena, nombre_visible, avatar, fecha_creacion) FROM stdin;
\.


--
-- TOC entry 4973 (class 0 OID 24834)
-- Dependencies: 229
-- Data for Name: vistas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vistas (id, usuario_id, tmdb_id, fecha_vista) FROM stdin;
\.


--
-- TOC entry 4985 (class 0 OID 0)
-- Dependencies: 222
-- Name: busquedas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.busquedas_id_seq', 1, false);


--
-- TOC entry 4986 (class 0 OID 0)
-- Dependencies: 226
-- Name: calificaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.calificaciones_id_seq', 1, false);


--
-- TOC entry 4987 (class 0 OID 0)
-- Dependencies: 219
-- Name: generos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.generos_id_seq', 12, true);


--
-- TOC entry 4988 (class 0 OID 0)
-- Dependencies: 224
-- Name: recomendaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.recomendaciones_id_seq', 1, false);


--
-- TOC entry 4989 (class 0 OID 0)
-- Dependencies: 217
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 1, false);


--
-- TOC entry 4990 (class 0 OID 0)
-- Dependencies: 228
-- Name: vistas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vistas_id_seq', 1, false);


--
-- TOC entry 4798 (class 2606 OID 24802)
-- Name: busquedas busquedas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.busquedas
    ADD CONSTRAINT busquedas_pkey PRIMARY KEY (id);


--
-- TOC entry 4802 (class 2606 OID 24827)
-- Name: calificaciones calificaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT calificaciones_pkey PRIMARY KEY (id);


--
-- TOC entry 4792 (class 2606 OID 24779)
-- Name: generos generos_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.generos
    ADD CONSTRAINT generos_nombre_key UNIQUE (nombre);


--
-- TOC entry 4794 (class 2606 OID 24777)
-- Name: generos generos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.generos
    ADD CONSTRAINT generos_pkey PRIMARY KEY (id);


--
-- TOC entry 4800 (class 2606 OID 24814)
-- Name: recomendaciones recomendaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recomendaciones
    ADD CONSTRAINT recomendaciones_pkey PRIMARY KEY (id);


--
-- TOC entry 4804 (class 2606 OID 24849)
-- Name: calificaciones unique_usuario_pelicula; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT unique_usuario_pelicula UNIQUE (usuario_id, tmdb_id);


--
-- TOC entry 4806 (class 2606 OID 24851)
-- Name: vistas unique_vista; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vistas
    ADD CONSTRAINT unique_vista UNIQUE (usuario_id, tmdb_id);


--
-- TOC entry 4796 (class 2606 OID 24784)
-- Name: usuario_generos usuario_generos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_generos
    ADD CONSTRAINT usuario_generos_pkey PRIMARY KEY (usuario_id, genero_id);


--
-- TOC entry 4786 (class 2606 OID 24770)
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- TOC entry 4788 (class 2606 OID 24768)
-- Name: usuarios usuarios_nombre_usuario_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_nombre_usuario_key UNIQUE (nombre_usuario);


--
-- TOC entry 4790 (class 2606 OID 24766)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 4808 (class 2606 OID 24840)
-- Name: vistas vistas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vistas
    ADD CONSTRAINT vistas_pkey PRIMARY KEY (id);


--
-- TOC entry 4811 (class 2606 OID 24803)
-- Name: busquedas busquedas_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.busquedas
    ADD CONSTRAINT busquedas_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 4813 (class 2606 OID 24828)
-- Name: calificaciones calificaciones_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calificaciones
    ADD CONSTRAINT calificaciones_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 4812 (class 2606 OID 24815)
-- Name: recomendaciones recomendaciones_busqueda_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recomendaciones
    ADD CONSTRAINT recomendaciones_busqueda_id_fkey FOREIGN KEY (busqueda_id) REFERENCES public.busquedas(id) ON DELETE CASCADE;


--
-- TOC entry 4809 (class 2606 OID 24790)
-- Name: usuario_generos usuario_generos_genero_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_generos
    ADD CONSTRAINT usuario_generos_genero_id_fkey FOREIGN KEY (genero_id) REFERENCES public.generos(id) ON DELETE CASCADE;


--
-- TOC entry 4810 (class 2606 OID 24785)
-- Name: usuario_generos usuario_generos_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuario_generos
    ADD CONSTRAINT usuario_generos_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 4814 (class 2606 OID 24841)
-- Name: vistas vistas_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vistas
    ADD CONSTRAINT vistas_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


-- Completed on 2026-03-10 20:41:41

--
-- PostgreSQL database dump complete
--

\unrestrict gDcIosNimpeWCRIcQpGAAVaElyNN56aTaiqMafilGRoHCbZCHbgpLPP3RVuFmSj

