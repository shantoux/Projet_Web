#!/usr/bin/env bash

# parse .projetWEB.ini
while read -r line; do
  if [[ $line =~ ^(host) ]]; then
    host=${line##* }
  elif [[ $line =~ ^(user) ]]; then
    user=${line##* }
  elif [[ $line =~ ^(password) ]]; then
    pw=${line##* }
  fi
done < website/.projetWEB.ini

# retrieve genome name
gen_name="${1}.fa"

# parse new genome
cd database/
python parser_bd.py $gen_name
cd ../

# add new genome to database
PGPASSWORD=$pw psql -h $host -U $user  << EOF
  SET SCHEMA 'annotation_seq';
  \i database/instances_new_genome.sql
  \q
EOF
