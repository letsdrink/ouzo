DROP TABLE IF EXISTS order_products CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS categories CASCADE;

CREATE TABLE categories (
  id SERIAL PRIMARY KEY,
  id_parent INTEGER REFERENCES categories,
  name        TEXT
);

CREATE TABLE manufacturers (
  id SERIAL PRIMARY KEY,
  name     TEXT
);

CREATE TABLE products (
  id  SERIAL PRIMARY KEY,
  id_category INTEGER REFERENCES categories,
  id_manufacturer INTEGER REFERENCES manufacturers,
  name        TEXT,
  description TEXT,
  sale        BOOLEAN
);

CREATE TABLE orders (
  id_order SERIAL PRIMARY KEY,
  name     TEXT
);

CREATE TABLE order_products (
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