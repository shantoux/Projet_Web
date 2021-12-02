CREATE SCHEMA annotation_seq;
SET SCHEMA 'annotation_seq';


CREATE TABLE users(
  email VARCHAR,
  pw VARCHAR,
  first_name VARCHAR,
  last_name VARCHAR,
  phone VARCHAR,
  role VARCHAR,
  status VARCHAR,
  date_of_validation TIMESTAMP,
  CHECK (role IN ('reader', 'annotator', 'validator', 'administrator')),
  CHECK (status IN ('waiting', 'validated')),
  PRIMARY KEY (email)
);

CREATE TABLE gene(
  gene_id VARCHAR,
  genome_id VARCHAR,
  begin_seq INTEGER,
  end_seg INTEGER,
  chromosome VARCHAR,
  localization VARCHAR,
  type VARCHAR,
  prot_seq VARCHAR,
  gene_seq VARCHAR,
  CHECK (type IN ('pep', 'cds')),
  PRIMARY KEY (gene_id),
  FOREIGN KEY (genome_id) REFERENCES genome(genome_id)
);

CREATE TABLE annotations(
  genome_id VARCHAR,
  gene_id VARCHAR,
  gene_biotype VARCHAR,
  transcript_biotype VARCHAR,
  gene_symbol VARCHAR,
  description VARCHAR,
  annotator VARCHAR,
  status VARCHAR,
  comments VARCHAR, -- les commentaires qui vont avec la validation d'une annotation
  -- mais il faut aussi garder les annotations refusées, donc des attributs différents ?
  -- du genre "discarded_comments" ou "discarded_annotation"
  -- je pense une autre table serait de bon goût
  date_of_validation TIMESTAMP,
  CHECK (status IN ('waiting', 'validated')),
  PRIMARY KEY (annotator, genome_id, gene_id),
  FOREIGN KEY (annotator) REFERENCES users(email),
  FOREIGN KEY (genome_id) REFERENCES genome(genome_id),
  FOREIGN KEY (gene_id) REFERENCES gene(gene_id)
);
