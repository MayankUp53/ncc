--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: crm_render_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crm_render_history (
    id character varying,
    render_start_time timestamp without time zone,
    render_end_time timestamp without time zone,
    total_render_time character varying,
    date_added timestamp without time zone DEFAULT now()
);


ALTER TABLE public.crm_render_history OWNER TO postgres;

--
-- Name: crm_serve_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crm_serve_history (
    id character varying,
    requested_url character varying,
    phone character varying,
    campaign character varying,
    session_id character varying,
    user_id character varying,
    serve_start_time timestamp without time zone,
    serve_end_time timestamp without time zone,
    total_serve_time character varying,
    date_added timestamp without time zone DEFAULT now(),
    push_sent_time timestamp without time zone,
    push_received_time timestamp without time zone,
    client_server_time_diff character varying NOT NULL,
    network_latency character varying NOT NULL
);


ALTER TABLE public.crm_serve_history OWNER TO postgres;


--
-- PostgreSQL database dump complete
--

