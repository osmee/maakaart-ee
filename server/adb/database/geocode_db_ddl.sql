-- Table: aadress_komponendid

-- DROP TABLE aadress_komponendid;

CREATE TABLE aadress_komponendid
(
  komp_id integer NOT NULL,
  tase integer,
  kood character(4),
  nimetus character varying(200),
  nimetus_liigiga character varying(200),
  ylemkomp_tase integer,
  ylemkomp_kood character(4),
  CONSTRAINT aadress_komponendid_pkey PRIMARY KEY (komp_id)
)
WITH (OIDS=FALSE);
ALTER TABLE aadress_komponendid OWNER TO postgres;

-- Table: aadress_objektid

-- DROP TABLE aadress_objektid;

CREATE TABLE aadress_objektid
(
  adob_id integer NOT NULL,
  ads_oid character varying(10),
  orig_tunnus character varying(100),
  kehtiv_alates character(14),
  taisaadress character varying(1000),
  lahiaadress character varying(1000),
  CONSTRAINT aadress_objektid_pkey PRIMARY KEY (adob_id)
)
WITH (OIDS=FALSE);
ALTER TABLE aadress_objektid OWNER TO postgres;


-- Table: aadressid

-- DROP TABLE aadressid;

CREATE TABLE aadressid
(
  adr_id integer NOT NULL,
  koodaadress character varying(45),
  tase1_id integer,
  tase2_id integer,
  tase3_id integer,
  tase4_id integer,
  tase5_id integer,
  tase6_id integer,
  tase7_id integer,
  tase8_id integer,
  taisaadress character varying(200),
  lahiaadress character varying(200),
  viitepunkt_x character varying(12),
  viitepunkt_y character varying(12),
  olek character varying(1),
  aadr_tekst character varying(70),
  CONSTRAINT id_pk PRIMARY KEY (adr_id)
)
WITH (OIDS=FALSE);
ALTER TABLE aadressid OWNER TO postgres;


-- Table: adkomp_synonyymid

-- DROP TABLE adkomp_synonyymid;

CREATE TABLE adkomp_synonyymid
(
  id integer NOT NULL,
  adkomp_tase integer,
  adkomp_kood character(4),
  otsistring character varying(200),
  gaz integer,
  CONSTRAINT adokomp_synonyymid_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE adkomp_synonyymid OWNER TO postgres;


-- Table: adob_aadressid

-- DROP TABLE adob_aadressid;

CREATE TABLE adob_aadressid
(
  adob_id integer NOT NULL,
  adr_id integer NOT NULL,
  viitepunkt_x character varying(12),
  viitepunkt_y character varying(12),
  CONSTRAINT adob_aadressid_pkey PRIMARY KEY (adob_id, adr_id)
)
WITH (OIDS=FALSE);
ALTER TABLE adob_aadressid OWNER TO postgres;

-- Table: test_requests

-- DROP TABLE test_requests;

CREATE TABLE test_requests
(
  raddress character varying(200),
  id serial NOT NULL,
  taisaadress character varying(200),
  code character varying(6),
  dur_s numeric,
  sql character varying(300),
  CONSTRAINT test_requests_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE test_requests OWNER TO postgres;

-- Table: ads_objtyybid

DROP TABLE ads_objtyybid;

CREATE TABLE ads_objtyybid
(
  kood character varying(2) NOT NULL,
  nimetus character varying(30),
  unik boolean,
  inittase character varying(30),
  mintase numeric,
  maxtase numeric,
  origregister character varying(100),
  CONSTRAINT objtyyp_pk PRIMARY KEY (kood)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE ads_objtyybid OWNER TO postgres;

-- Table: ma_komponendid

-- DROP TABLE ma_komponendid;

CREATE TABLE ma_komponendid
(
  ma_komp_id numeric,
  ylemkomp_id numeric,
  tase numeric,
  ametlik_kood character varying,
  ametlik_komp_id numeric,
  nimetus character varying,
  kehtetu character varying
)
WITH (
  OIDS=FALSE
);
ALTER TABLE ma_komponendid OWNER TO postgres;

-- Table: ma_objektid

-- DROP TABLE ma_objektid;

CREATE TABLE ma_objektid
(
  ma_adob_id character varying,
  ma_adob_liik character varying,
  ametlik_liik character varying,
  ads_oid character varying,
  orig_tunnus character varying,
  ma_adr_id character varying,
  taisaadress character varying,
  lahiaadress character varying,
  viitepunkt_x character varying,
  viitepunkt_y character varying,
  tase1_id character varying,
  tase2_id character varying,
  tase3_id character varying,
  tase4_id character varying,
  tase5_id character varying,
  tase6_id character varying,
  tase7_id character varying,
  tase8_id character varying
)
WITH (
  OIDS=FALSE
);
ALTER TABLE ma_objektid OWNER TO postgres;

-- sihtnumbrid, csv failist www.post.ee saidilt (10.12.2010 seisuga toiminud formaat)

-- Table: sihtnumbrid

-- DROP TABLE sihtnumbrid;

CREATE TABLE sihtnumbrid
(
  maakond character varying,
  vald character varying,
  asula character varying,
  tanav character varying,
  aadressiliik character varying,
  majaalgus character varying,
  majalopp character varying,
  sihtnumber character varying
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sihtnumbrid OWNER TO jaakl;

