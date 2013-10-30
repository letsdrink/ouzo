DROP TABLE IF EXISTS order_products;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS manufacturers CASCADE;

CREATE TABLE categories (
  id INTEGER PRIMARY KEY,
  id_parent INTEGER REFERENCES categories (id),
  name        TEXT
);

CREATE TABLE manufacturers (
  id INTEGER PRIMARY KEY,
  name     TEXT
);

CREATE TABLE products (
  id  INTEGER PRIMARY KEY,
  id_category INTEGER REFERENCES categories (id),
  id_manufacturer INTEGER REFERENCES manufacturers(id),
  name        TEXT,
  description TEXT,
  sale        BOOLEAN
);

CREATE TABLE orders (
  id_order INTEGER PRIMARY KEY,
  name     TEXT
);

CREATE TABLE order_products (
  id_order          INTEGER REFERENCES orders (id_order),
  id_product        INTEGER REFERENCES products (id)
);
