DROP TABLE IF EXISTS order_products CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS categories CASCADE;

CREATE TABLE categories (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_parent INTEGER REFERENCES categories (id_category),
  name        TEXT
);

CREATE TABLE products (
  id  INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_category INTEGER REFERENCES categories (id_category),
  name        TEXT,
  description TEXT,
  sale        BOOLEAN
);

CREATE TABLE orders (
  id_order INTEGER AUTO_INCREMENT PRIMARY KEY,
  name     TEXT
);

CREATE TABLE order_products (
  id_order          INTEGER REFERENCES orders (id_order),
  id_product        INTEGER REFERENCES products (id)
);

DROP FUNCTION IF EXISTS get_name;
DELIMITER $$
CREATE FUNCTION get_name(p_name TEXT)
  RETURNS TEXT LANGUAGE SQL
  BEGIN
    RETURN (SELECT
              name
            FROM categories
            WHERE name = p_name);
  END;
$$
DELIMITER ;