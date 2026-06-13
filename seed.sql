/* =======================================================================
   seed.sql  –  Données de démonstration Frigo App
   ======================================================================= */

USE hemidjle_frigo;
-- Nettoyage si le script est relancé plusieurs fois
DELETE FROM recipes;
DELETE FROM ingredients;
DELETE FROM users WHERE email = 'admin@local';

/* ----------------------- Compte ADMIN --------------------------------- */
INSERT IGNORE INTO users (email, password_hash, role)
VALUES (
  'admin@local',
  '$2y$10$sF9Nrzxo/.AoaAx5iQqVDuBxslw6OwY2GPc2g9qN/hWPjQSZa/lsm',
  'admin'
);
SET @ADMIN_ID := (SELECT id FROM users WHERE email = 'admin@local');

/* ----------------------- Ingrédients ---------------------------------- */
INSERT INTO ingredients (user_id, name, quantity, expiry_date, category) VALUES
(@ADMIN_ID, 'Tomate', 3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'Légume'),
(@ADMIN_ID, 'Carotte', 5, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Légume'),
(@ADMIN_ID, 'Brocoli', 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Légume'),
(@ADMIN_ID, 'Poulet', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Viande'),
(@ADMIN_ID, 'Boeuf haché', 1, DATE_ADD(CURDATE(), INTERVAL -1 DAY), 'Viande'),
(@ADMIN_ID, 'Fromage', 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Produit laitier'),
(@ADMIN_ID, 'Lait', 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Produit laitier'),
(@ADMIN_ID, 'Pain', 4, DATE_ADD(CURDATE(), INTERVAL 6 DAY), 'Céréale'),
(@ADMIN_ID, 'Riz', 10, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'Céréale'),
(@ADMIN_ID, 'Pâtes', 8, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'Céréale'),
(@ADMIN_ID, 'Pomme', 6, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Fruit'),
(@ADMIN_ID, 'Banane', 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Fruit'),
(@ADMIN_ID, 'Basilic', 1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Herbe'),
(@ADMIN_ID, 'Origan', 1, DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Herbe'),
(@ADMIN_ID, 'Farine', 3, DATE_ADD(CURDATE(), INTERVAL 180 DAY), 'Épicerie'),
(@ADMIN_ID, 'Sucre', 5, DATE_ADD(CURDATE(), INTERVAL 300 DAY), 'Épicerie'),
(@ADMIN_ID, 'Huile d\'olive', 1, DATE_ADD(CURDATE(), INTERVAL 365 DAY), 'Épicerie'),
(@ADMIN_ID, 'Yaourt', 2, DATE_ADD(CURDATE(), INTERVAL -2 DAY), 'Produit laitier');

/* ----------------------- Recettes ------------------------------------- */
INSERT INTO recipes (user_id, name, ingredients_js) VALUES
(@ADMIN_ID, 'Salade tomate-fromage',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Tomate','quantity',2),
   JSON_OBJECT('ingredient','Fromage','quantity',1)
 )),
(@ADMIN_ID, 'Poulet rôti',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Poulet','quantity',1),
   JSON_OBJECT('ingredient','Basilic','quantity',1)
 )),
(@ADMIN_ID, 'Spaghetti bolognaise',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Pâtes','quantity',2),
   JSON_OBJECT('ingredient','Boeuf haché','quantity',1),
   JSON_OBJECT('ingredient','Tomate','quantity',1)
 )),
(@ADMIN_ID, 'Sandwich poulet',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Poulet','quantity',1),
   JSON_OBJECT('ingredient','Pain','quantity',2),
   JSON_OBJECT('ingredient','Fromage','quantity',1)
 )),
(@ADMIN_ID, 'Riz sauté',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Riz','quantity',2),
   JSON_OBJECT('ingredient','Carotte','quantity',1),
   JSON_OBJECT('ingredient','Brocoli','quantity',1)
 )),
(@ADMIN_ID, 'Smoothie banane',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Banane','quantity',2),
   JSON_OBJECT('ingredient','Lait','quantity',1),
   JSON_OBJECT('ingredient','Yaourt','quantity',1)
 )),
(@ADMIN_ID, 'Gâteau yaourt',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Yaourt','quantity',1),
   JSON_OBJECT('ingredient','Farine','quantity',2),
   JSON_OBJECT('ingredient','Sucre','quantity',1)
 )),
(@ADMIN_ID, 'Pommes caramélisées',
 JSON_ARRAY(
   JSON_OBJECT('ingredient','Pomme','quantity',3),
   JSON_OBJECT('ingredient','Sucre','quantity',1)
 ));
