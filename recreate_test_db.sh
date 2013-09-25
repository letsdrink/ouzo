USER=postgres
HOST=127.0.0.1
PORT=5432
DB_NAME=ouzo_test

#createdb -e -U postgres ouzo_test

psql -v ON_ERROR_STOP=1 -e -U $USER -h $HOST -p $PORT -f test/test-db/recreate_schema.sql $DB_NAME
