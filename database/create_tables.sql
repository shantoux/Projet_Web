CREATE SCHEMA database_projet;
SET SCHEMA 'database_projet';

CREATE TABLE users(
  email VARCHAR,
  pw VARCHAR,
  last_name VARCHAR,
  first_name VARCHAR,
  phone VARCHAR,
  role VARCHAR,
  status VARCHAR,
  CHECK (role IN ('Reader', 'Annotator', 'Validator', 'Administrator')),
  CHECK (status IN ('waiting', 'validated')),
  PRIMARY KEY (email)
);

CREATE TABLE genome(
  genome_id VARCHAR,
  genome_seq VARCHAR,
  PRIMARY KEY (genome_id)
);

CREATE TABLE gene(
  sequence_id VARCHAR,
  genome_id VARCHAR,
  start_seq INTEGER,
  end_seq INTEGER,
  chromosome VARCHAR,
  prot_seq VARCHAR(100000),
  gene_seq VARCHAR(100000),
  PRIMARY KEY (sequence_id),
  FOREIGN KEY (genome_id) REFERENCES genome(genome_id)
);


CREATE TABLE annotations(
  genome_id VARCHAR,
  gene_id VARCHAR,
  sequence_id VARCHAR,
  gene_biotype VARCHAR,
  transcript_biotype VARCHAR,
  gene_symbol VARCHAR,
  description VARCHAR,
  annotator VARCHAR,
  status VARCHAR,
  comments VARCHAR,
  attempt INTEGER DEFAULT 1,
  CHECK (status IN ('waiting', 'validated', 'rejected')),
  PRIMARY KEY (annotator, genome_id, sequence_id, attempt),
  FOREIGN KEY (annotator) REFERENCES users(email),
  FOREIGN KEY (genome_id) REFERENCES genome(genome_id),
  FOREIGN KEY (sequence_id) REFERENCES gene(sequence_id)
);

-- stores all topics of the annotator forum
CREATE TABLE topics(
  name VARCHAR,
  creation_date TIMESTAMP DEFAULT now(),
  PRIMARY KEY (name)
);

-- stores who can talk in which conversation in the annotator forum
CREATE TABLE correspondents(
  topic_name VARCHAR,
  user_email VARCHAR,
  PRIMARY KEY (topic_name, user_email),
  FOREIGN KEY (topic_name) REFERENCES topics(name),
  FOREIGN KEY (user_email) REFERENCES users(email)
);

-- stores messages of all conversations in the annotator forum
CREATE TABLE messages(
  topic_name VARCHAR,
  user_email VARCHAR,
  message VARCHAR,
  emission_date TIMESTAMP DEFAULT now(),
  PRIMARY KEY (topic_name, user_email, emission_date),
  FOREIGN KEY (topic_name) REFERENCES topics(name),
  FOREIGN KEY (user_email) REFERENCES users(email)
);
