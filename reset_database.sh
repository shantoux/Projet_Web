#!/usr/bin/env bash

# parse .bd_postrgre
while read -r line; do
  if [[ $line =~ ^(host) ]]; then
    host=${line##* }
  elif [[ $line =~ ^(user) ]]; then
    user=${line##* }
  elif [[ $line =~ ^(password) ]]; then
    pw=${line##* }
  fi
done < $1

echo $host
echo $user
echo $pw

PGPASSWORD=$pw psql -h $host -U $user  << EOF
  \i database/drop_schema.sql
  \i database/create_tables.sql
  \i database/instances_cft073.sql
  \i database/instances_edl933.sql
  \i database/instances_mg1655.sql
  \i database/instances_unannotated.sql
  \i database/instances_users.sql
  \q
EOF
