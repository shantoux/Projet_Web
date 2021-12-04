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

# connect to and initialize database
PGPASSWORD=$pw psql -h $host -U $user  << EOF
  \i database/drop_schema.sql
  \i database/create_tables.sql
  \i database/instances_users.sql
  \i database/instances_cft073.sql
  \i database/instances_edl933.sql
  \i database/instances_mg1655.sql
  \i database/instances_unannotated.sql
  \q
EOF
