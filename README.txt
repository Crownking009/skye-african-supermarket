SKYE AFRICAN SUPERMARKET - Phase 1
1. cPanel > MySQL Databases: create a database + user. Open phpMyAdmin, run schema.sql.
2. Edit config.php (database details, admin password).
3. Upload everything to public_html (or a subfolder).
4. Visit /admin/import.php, sign in, upload skye_products_seed.csv.
6. Run schema_phase6.sql (new tables, adds promo columns to orders). Upload .htaccess too (hides .sql/.csv files and sets sitemap.xml and robots.txt).
7. After importing products, run seed_recipes.sql for 3 starter recipes (schema.sql already has all tables).
7a. Run schema_phase9.sql in phpMyAdmin to add the blog and publish four starter stories. Manage stories under Admin > Blog stories.
5. Put the logo at assets/logo.png and product photos at assets/products/SKU.jpg (e.g. SKY-0001.jpg).
8. (Phone photos are big: .user.ini raises the upload limit to 12 MB per image. If uploads still fail, raise upload_max_filesize and post_max_size in cPanel > MultiPHP INI Editor.)
8. Sign in at /admin/ with the config.php password, then create your owner login under Staff logins.
9. Under Settings: free-delivery amount, shop WhatsApp number, top-of-site message.
10. Category photos (optional): save as assets/categories/<aisle-slug>.jpg, e.g. fresh-produce.jpg, beauty-hair.jpg, drinks.jpg. Without them the shop shows an icon.
11. SEO: after launch, add the site to Google Search Console and submit https://YOURDOMAIN/sitemap.xml. Claim and complete your Google Business Profile with the same address, phone number and opening hours as the website.
12. Optional: replace assets/og.jpg with a 1200x630 picture of the shop. It is the image shown when the site is shared on WhatsApp and social media.
