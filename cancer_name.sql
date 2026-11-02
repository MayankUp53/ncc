--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: cancer_name; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE cancer_name (
    cancer_id integer NOT NULL,
    cancer_name character varying NOT NULL,
    display_id integer
);


ALTER TABLE public.cancer_name OWNER TO postgres;

--
-- Name: cancer_name_cancer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE cancer_name_cancer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cancer_name_cancer_id_seq OWNER TO postgres;

--
-- Name: cancer_name_cancer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE cancer_name_cancer_id_seq OWNED BY cancer_name.cancer_id;


--
-- Name: cancer_name_cancer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('cancer_name_cancer_id_seq', 40, true);


--
-- Name: cancer_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY cancer_name ALTER COLUMN cancer_id SET DEFAULT nextval('cancer_name_cancer_id_seq'::regclass);


--
-- Data for Name: cancer_name; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY cancer_name (cancer_id, cancer_name, display_id) FROM stdin;
1	不明	1
2	胃がん	2
3	咽頭がん	3
4	カルチノイド	4
5	肝細胞がん	5
6	胸腺がん	6
7	血液がん	7
8	口腔がん	8
9	甲状腺がん	9
10	喉頭がん	10
11	骨・軟部肉腫	11
12	子宮頸がん	12
13	子宮体がん	13
14	子宮その他	14
15	GIST	15
16	十二指腸がん	16
17	小腸がん	17
18	小児がん	18
19	食道がん	19
20	腎細胞がん	20
21	腎盂尿管がん	21
22	膵臓がん	22
23	精巣腫瘍	23
24	前立腺がん	24
25	大腸がん	25
26	胆のう胆管がん	26
27	中皮腫	27
28	乳がん	28
29	脳腫瘍	29
30	肺がん	30
31	皮膚がん	31
32	副腎がん	32
33	腹膜がん	33
34	膀胱がん	34
35	眼のがん	35
36	卵巣がん	36
37	原発不明がん	37
38	上記がん種分類不可	38
39	疑い	39
40	その他	40
\.


--
-- Name: cancer_name_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY cancer_name
    ADD CONSTRAINT cancer_name_pkey PRIMARY KEY (cancer_id);


--
-- PostgreSQL database dump complete
--

