#!/bin/bash
until PGPASSWORD=mypassword psql -h "delain_dbtu" -U "delain" -c '\q'; do
  >&2 echo "Postgres is unavailable - sleeping"
  sleep 1
done
echo "Postgres UP !"