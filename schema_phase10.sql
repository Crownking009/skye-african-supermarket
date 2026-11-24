CREATE TABLE IF NOT EXISTS product_category_map(
 product_id INT NOT NULL,
 category_id INT NOT NULL,
 PRIMARY KEY(product_id,category_id),
 KEY(category_id),
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
 FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE products ADD COLUMN description TEXT NULL;

INSERT IGNORE INTO product_category_map(product_id,category_id)
SELECT id,category_id FROM products;

