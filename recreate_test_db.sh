USER=thulium_1
HOST=127.0.0.1
PORT=5432
DB_NAME=framework_test

#createdb -e -U postgres -O thulium_1 framework_test

psql -v ON_ERROR_STOP=1 -e -U $USER -h $HOST -p $PORT -f test/test-db/recreate_schema.sql $DB_NAME
