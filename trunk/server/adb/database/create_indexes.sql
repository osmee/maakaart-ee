-- Index: ind_adk_kood

-- DROP INDEX ind_adk_kood;

CREATE INDEX ind_adk_kood
  ON aadress_komponendid
  USING btree
  (kood);
  
  -- Index: ind_lahiaadr2

-- DROP INDEX ind_lahiaadr2;

CREATE INDEX ind_lahiaadr2
  ON aadress_objektid
  USING btree
  (lahiaadress text_pattern_ops);

-- Index: indo_lahi2

-- DROP INDEX indo_lahi2;

CREATE INDEX indo_lahi2
  ON aadress_objektid
  USING btree
  (lahiaadress text_pattern_ops);

-- Index: indo_oid

-- DROP INDEX indo_oid;

CREATE INDEX indo_oid
  ON aadress_objektid
  USING btree
  (ads_oid text_pattern_ops);

  
-- Index: ind_lahi2

-- DROP INDEX ind_lahi2;

CREATE INDEX ind_lahi2
  ON aadressid
  USING btree
  (lahiaadress text_pattern_ops);

-- Index: ind_lahiaadr

-- DROP INDEX ind_lahiaadr;

CREATE INDEX ind_lahiaadr
  ON aadressid
  USING btree
  (lahiaadress);

-- Index: ind_t1

-- DROP INDEX ind_t1;

CREATE INDEX ind_t1
  ON aadressid
  USING btree
  (tase1_id);

-- Index: ind_t4

-- DROP INDEX ind_t4;

CREATE INDEX ind_t4
  ON aadressid
  USING btree
  (tase4_id);

-- Index: ind_t5

-- DROP INDEX ind_t5;

CREATE INDEX ind_t5
  ON aadressid
  USING btree
  (tase5_id);

-- Index: ind_t6

-- DROP INDEX ind_t6;

CREATE INDEX ind_t6
  ON aadressid
  USING btree
  (tase6_id);

-- Index: ind_t7

-- DROP INDEX ind_t7;

CREATE INDEX ind_t7
  ON aadressid
  USING btree
  (tase7_id);

-- Index: ind_t8

-- DROP INDEX ind_t8;

CREATE INDEX ind_t8
  ON aadressid
  USING btree
  (tase8_id);

-- Index: ind_tais2

-- DROP INDEX ind_tais2;

CREATE INDEX ind_tais2
  ON aadressid
  USING btree
  (taisaadress text_pattern_ops);

-- Index: ind_taisaadress

-- DROP INDEX ind_taisaadress;

CREATE INDEX ind_taisaadress
  ON aadressid
  USING btree
  (taisaadress);
  
  -- Index: adkomp_ind1

-- DROP INDEX adkomp_ind1;

CREATE INDEX adkomp_ind1
  ON adkomp_synonyymid
  USING btree
  (otsistring text_pattern_ops);

  
  -- Index: sihtnumber_veerud

-- DROP INDEX sihtnumber_veerud;

CREATE INDEX sihtnumber_veerud
  ON sihtnumbrid
  USING btree
  (asula, tanav, majaalgus, majalopp);
