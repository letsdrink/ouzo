DROP TABLE IF EXISTS order_products CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS categories CASCADE;

CREATE TABLE categories (
  id_category SERIAL PRIMARY KEY,
  name        TEXT
);

CREATE TABLE products (
  id_product  SERIAL PRIMARY KEY,

  id_category INTEGER REFERENCES categories,
  name        TEXT,
  description TEXT
);

CREATE TABLE orders (
  id_order SERIAL PRIMARY KEY,
  name     TEXT
);

CREATE TABLE order_products (
  id_order_products SERIAL PRIMARY KEY,
  id_order          INTEGER REFERENCES orders,
  id_product        INTEGER REFERENCES products
);

CREATE OR REPLACE FUNCTION get_name(TEXT)
  RETURNS TEXT
LANGUAGE plpgsql
AS $_$
DECLARE
  p_name ALIAS FOR $1;
  return_name TEXT;
BEGIN
  SELECT
    name
  INTO return_name
  FROM categories
  WHERE name = p_name;

  RETURN return_name;
END;
$_$;