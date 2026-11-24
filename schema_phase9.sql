CREATE TABLE IF NOT EXISTS blog_posts(
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL,
 slug VARCHAR(200) NOT NULL UNIQUE,
 excerpt VARCHAR(500) NOT NULL,
 content MEDIUMTEXT NOT NULL,
 cover_image VARCHAR(500) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO blog_posts(title,slug,excerpt,content,active) VALUES
('Building a West African pantry','building-a-west-african-pantry','A practical guide to the grains, flours, oils and seasonings that make everyday West African cooking easier.','Start with ingredients you already cook with. Rice, garri, semolina, beans and yam can form the base of many meals. Add to your cupboard as you discover new dishes.\n\nPalm oil, ground crayfish, dried fish, egusi and familiar spice blends bring depth to soups and stews. Keep ingredients sealed and labelled so they stay fresh and easy to find.\n\nA short list for two or three meals helps avoid waste. Browse our pantry aisles and add what you need to your basket, then collect in store or ask us about delivery.',1),
('How to choose plantain for every dish','how-to-choose-fresh-plantain','The colour of a plantain is a helpful guide to its texture and sweetness. Choose the right one for the way you want to cook.','Green plantains are firm and starchy. They work well boiled, roasted or in savoury dishes where you want the plantain to hold its shape.\n\nAs the skin turns yellow, the fruit softens and becomes sweeter. Yellow plantains are a good choice when you want a balance of sweetness and firmness.\n\nA plantain with dark patches is softer and sweeter, making it a popular choice for frying. Choose fruit that feels firm enough to handle and has no soft or damaged areas.',1),
('A simple guide to making egusi soup','making-egusi-soup-at-home','Egusi soup is built around ground melon seeds, a flavourful base and the greens you enjoy. Use this simple order of steps as a starting point.','Gather ground egusi, palm oil, stock, your choice of meat or fish, seasonings and leafy greens. Wash and prepare fresh ingredients before you begin.\n\nCook your protein until tender and keep the stock. Warm palm oil, add the egusi and stir gently, then add stock a little at a time until the mixture reaches the texture you prefer.\n\nAdd your cooked protein and season to taste. Stir in the greens near the end so they keep their colour. Serve with a swallow or another favourite accompaniment.',1),
('Getting started with Nigerian soups','getting-started-with-nigerian-soups','A few pantry staples can help you prepare a variety of Nigerian soups at home. Start with your favourites and build from there.','Many Nigerian soups begin with a flavourful stock, a choice of protein and ingredients such as palm oil, crayfish, peppers or locust beans. The exact combination changes with the soup and the cook.\n\nEgusi, ogbono and vegetable soups each have their own texture and ingredients. Choose one to learn first, then keep a note of the quantities and greens your household enjoys.\n\nFresh vegetables and pantry staples are easy to combine when you plan ahead. Browse our aisles for the ingredients you need and ask the team if you are looking for something specific.',1);
