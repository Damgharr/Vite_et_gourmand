INSERT INTO allergen (label) VALUES
('Gluten'),
('Lactose'),
('Crustacés'),
('Poisson'),
('Soja'),
('Sésame'),
('Fruits à coque'),
('Œufs'),
('Mollusques');

INSERT INTO dish (title, description, photo) VALUES
('Bouchée pesto tomate', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/bouchee_pesto_tomate.jpg')),
('Brochette poulet mexicain', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/brochette_poulet_mexicain.jpg')),
('Bruschetta pepperoni', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/bruschetta_pepperoni.jpg')),
('Buffet légume viande grillée', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/buffet_legume_viande_grillée.jpg')),
('Burger poulet', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/burger_poulet.jpg')),
('Cheesecake caramel', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/cheesecake_caramel.jpg')),
('Crêpe fruit rouge', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/crepe_fruit_rouge.jpg')),
('Crevette persillée', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/crevette_persillee.jpg')),
('Grillade mix', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/grillade_mix.jpg')),
('Moule', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/moule.jpg')),
('Pancake noix', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/pancake_noix.jpg')),
('Plateau sushi', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/plateau_sushi.jpg')),
('Poisson patate', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/poisson_patate.jpg')),
('Poulet curry', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/poulet_curry.jpg')),
('Poulet tender', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/poulet_tender.jpg')),
('Salade d\'avocats graines', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/salade_vegan.jpg')),
('Saucisse poêlée', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/saucisse_poele.jpg')),
('Saumon soja', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/saumon_soja.jpg')),
('Takoyaki', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/takoyaki.jpg')),
('Tapas jambon fromage', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/tapas_jambon_fromage.jpg')),
('Tarte citron fruit', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/tarte_citron_fruit.jpg')),
('Tarte épinard lard', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/tarte_epinard_lard.jpg')),
('Mélange détox', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/vegan.jpg')),
('Velouté', 'Délicieux plat préparé avec soin par nos chefs', pg_read_binary_file('/var/lib/postgres/data/dish_img/veloute.jpg'));

INSERT INTO dish_allergen (dish_id, allergen_id)
SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Bouchée pesto tomate' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Bouchée pesto tomate' AND a.label = 'Fruits à coque'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Brochette poulet mexicain' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Brochette poulet mexicain' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Bruschetta pepperoni' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Bruschetta pepperoni' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Buffet légume viande grillée' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Buffet légume viande grillée' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Burger poulet' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Burger poulet' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Burger poulet' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Cheesecake caramel' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Cheesecake caramel' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Cheesecake caramel' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Crêpe fruit rouge' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Crêpe fruit rouge' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Crêpe fruit rouge' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Crevette persillée' AND a.label = 'Crustacés'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Grillade mix' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Grillade mix' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Moule' AND a.label = 'Mollusques'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Pancake noix' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Pancake noix' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Pancake noix' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Pancake noix' AND a.label = 'Fruits à coque'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Plateau sushi' AND a.label = 'Poisson'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Plateau sushi' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Plateau sushi' AND a.label = 'Sésame'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Poisson patate' AND a.label = 'Poisson'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Poulet curry' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Poulet curry' AND a.label = 'Fruits à coque'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Poulet tender' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Poulet tender' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Salade vegan' AND a.label = 'Sésame'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Salade vegan' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Saucisse poêlée' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Saumon soja' AND a.label = 'Poisson'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Saumon soja' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Takoyaki' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Takoyaki' AND a.label = 'Poisson'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Takoyaki' AND a.label = 'Mollusques'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Takoyaki' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tapas jambon fromage' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tapas jambon fromage' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte citron fruit' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte citron fruit' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte citron fruit' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte épinard lard' AND a.label = 'Gluten'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte épinard lard' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Tarte épinard lard' AND a.label = 'Œufs'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Vegan' AND a.label = 'Soja'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Vegan' AND a.label = 'Sésame'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Vegan' AND a.label = 'Fruits à coque'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Velouté' AND a.label = 'Lactose'
UNION ALL SELECT d.id, a.id FROM dish d, allergen a WHERE d.title = 'Velouté' AND a.label = 'Gluten';
