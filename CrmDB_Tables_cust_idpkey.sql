--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: crm_browser_render_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crm_browser_render_history (
    phone character varying,
    campaign_id character varying,
    unique_id character varying,
    rendering_starttime timestamp without time zone,
    renderingendtime timestamp without time zone
);


ALTER TABLE public.crm_browser_render_history OWNER TO postgres;

--
-- Name: crm_push_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crm_push_history (
    phone character varying,
    campaign_id character varying,
    unique_id character varying,
    push_time timestamp without time zone,
    crmpopup_time timestamp without time zone,
    databaserequesttime timestamp without time zone,
    databaseresponsetime timestamp without time zone,
    server_client_timediff character varying,
    userid character varying,
    sessionid character varying
);


ALTER TABLE public.crm_push_history OWNER TO postgres;

--
-- Name: crm_delay_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW crm_delay_view AS
    SELECT cph.userid, cph.phone, cph.campaign_id, cph.unique_id, cph.push_time, cph.crmpopup_time, cph.databaserequesttime, cph.databaseresponsetime, cbh.rendering_starttime, cbh.renderingendtime, (cph.databaseresponsetime - cph.push_time) AS serving_delay, (cbh.renderingendtime - cbh.rendering_starttime) AS render_delay, (cbh.renderingendtime - cph.crmpopup_time) AS total_client_delay FROM crm_browser_render_history cbh, crm_push_history cph WHERE ((((cph.unique_id)::text = (cbh.unique_id)::text) AND (cph.unique_id IS NOT NULL)) AND ((cph.unique_id)::text <> ''::text));


ALTER TABLE public.crm_delay_view OWNER TO postgres;

--
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE customer (
    customer_id character varying NOT NULL,
    phone1 character varying,
    phone2 character varying,
    phone3 character varying,
    date_added timestamp without time zone DEFAULT now() NOT NULL,
    updatedon character varying,
    feedback character varying,
    isnewnumber character varying DEFAULT 'no'::character varying NOT NULL,
    title character varying,
    fname character varying,
    mname character varying,
    lname character varying,
    designation character varying,
    emailid character varying,
    martial_status character varying,
    sex character varying,
    houseno character varying,
    streetno character varying,
    landmarks character varying,
    city character varying,
    state character varying,
    zip character varying,
    country character varying,
    bankname character varying,
    accno character varying,
    bankaccname character varying,
    sortcode character varying,
    ssno character varying,
    comments character varying,
    altno character varying,
    mobileno character varying,
    faxno character varying,
    occupation character varying,
    dob character varying,
    county character varying,
    companyname character varying,
    btnumber character varying,
    leadid character varying NOT NULL,
    homephone character varying,
    officephone character varying
);


ALTER TABLE public.customer OWNER TO postgres;

--
-- Name: customer_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE customer_history (
    customer_id character varying NOT NULL,
    session_id character varying,
    campaign_id character varying,
    crt_object_id character varying,
    usercrtobjectid character varying,
    phone1 character varying,
    phone2 character varying,
    phone3 character varying,
    queue_id character varying,
    queue_name character varying,
    call_date timestamp without time zone DEFAULT now() NOT NULL,
    verifiername character varying,
    disposition character varying,
    user_id character varying,
    phoneoriginal character varying,
    updatedon character varying,
    feedback character varying,
    isnewnumber character varying DEFAULT 'no'::character varying NOT NULL,
    title character varying,
    fname character varying,
    mname character varying,
    lname character varying,
    designation character varying,
    emailid character varying,
    martial_status character varying,
    sex character varying,
    houseno character varying,
    streetno character varying,
    landmarks character varying,
    city character varying,
    state character varying,
    zip character varying,
    country character varying,
    bankname character varying,
    accno character varying,
    bankaccname character varying,
    sortcode character varying,
    ssno character varying,
    comments character varying,
    altno character varying,
    mobileno character varying,
    faxno character varying,
    occupation character varying,
    dob character varying,
    county character varying,
    companyname character varying,
    btnumber character varying,
    leadid character varying NOT NULL,
    homephone character varying,
    officephone character varying,
    dispositionattr character varying
);


ALTER TABLE public.customer_history OWNER TO postgres;

--
-- Name: leads; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE leads (
    lid text NOT NULL,
    lname character varying
);


ALTER TABLE public.leads OWNER TO postgres;

--
-- Data for Name: crm_browser_render_history; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: crm_push_history; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: customer; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO customer VALUES ('101', '101', NULL, NULL, '2009-05-21 13:55:27.880097', '2009-06-10 10:05:21.019566+05:30', NULL, 'no', 'Mr', 'John', 'M.', 'Williams', NULL, 'jugalsaini@drishti-soft.com', NULL, NULL, '142', '8', 'Silani Gate ', 'Jhajjar', 'Haryana', '124103', 'India', NULL, NULL, NULL, NULL, NULL, NULL, '323233232', '99090900', '23232323', NULL, '31-03-1976', NULL, 'Xian Inc.', NULL, 'Default', '12112', '1002');


--
-- Data for Name: customer_history; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: leads; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO leads VALUES ('Default', 'Default');


--
-- Name: customer_customer_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_customer_id_key UNIQUE (customer_id, phone1);


--
-- Name: customer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (customer_id);


--
-- Name: leads_lid_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY leads
    ADD CONSTRAINT leads_lid_key UNIQUE (lid, lname);


--
-- Name: leads_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY leads
    ADD CONSTRAINT leads_pkey PRIMARY KEY (lid);


--
-- Name: customer_cusotmer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--
--
--ALTER TABLE ONLY customer_history
--    ADD CONSTRAINT customer_cusotmer_id_fkey FOREIGN KEY (customer_id) REFERENCES customer(customer_id);
--

--
-- Name: customer_leadid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_leadid_fkey FOREIGN KEY (leadid) REFERENCES leads(lid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

