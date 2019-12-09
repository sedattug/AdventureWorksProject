/*
* Created by ST on 08.12.2019 16:10
* 
*/

ALTER TABLE production.productreview
    ADD COLUMN reviewstatus integer NOT NULL DEFAULT 2;

COMMENT ON COLUMN production.productreview.reviewstatus
    IS '0 = Rejected, 1 = Approved, 2 = Pending';
	

	
--------------------------------------------------------

-- SEQUENCE: production.email_que_email_que_id_seq

-- DROP SEQUENCE production.email_que_email_que_id_seq;

CREATE SEQUENCE production.email_que_email_que_id_seq;

ALTER SEQUENCE production.email_que_email_que_id_seq
    OWNER TO postgres;
	
	
-------------------------------------------------------

-- Table: production.email_que

-- DROP TABLE production.email_que;

CREATE TABLE production.email_que
(
    email_que_id integer NOT NULL DEFAULT nextval('production.email_que_email_que_id_seq'::regclass),
    productreviewid integer NOT NULL,
    from_mail character varying(1024) COLLATE pg_catalog."default" NOT NULL DEFAULT 'noreply@adventure-works.com'::character varying,
    from_name character varying(1024) COLLATE pg_catalog."default" NOT NULL,
    to_mail character varying(1024) COLLATE pg_catalog."default" NOT NULL,
    subject character varying(1024) COLLATE pg_catalog."default",
    message character varying(3000) COLLATE pg_catalog."default",
    status smallint NOT NULL DEFAULT 0,
    trying_count integer NOT NULL DEFAULT 0,
    last_try_date timestamp without time zone,
    CONSTRAINT "PK_email_que" PRIMARY KEY (email_que_id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE production.email_que
    OWNER to postgres;