SKYE AFRICAN SUPERMARKET - Phase 1
1. cPanel > MySQL Databases: create a database + user. Open phpMyAdmin, run schema.sql.
2. Edit config.php (database details, admin password).
3. Upload everything to public_html (or a subfolder).
4. After running schema_phase10.sql, visit /admin/import.php, sign in, and upload the store product JSON list (the provided .txt file containing JSON is supported). JSON products receive CAT-#### SKUs. Numbered photos are matched as assets/products/product-<id>.(png, jpg, jpeg or webp).
6. Run schema_phase6.sql (new tables, adds promo columns to orders). Upload .htaccess too (hides .sql/.csv files and sets sitemap.xml and robots.txt).
7. After importing products, run seed_recipes.sql for 3 starter recipes (schema.sql already has all tables).
7a. Run schema_phase9.sql in phpMyAdmin to add the blog and publish four starter stories. Manage stories under Admin > Blog stories.
7b. Run schema_phase10.sql once in phpMyAdmin before importing the product JSON. It enables products to be assigned to every matching category and adds product descriptions. The admin login schema upgrades automatically when you open Admin.
7c. Run schema_phase11.sql once after schema_phase6.sql and before the first admin sign-in. It adds payment click tracking, refund tracking, admin recovery emails, request throttling and newsletter consent fields. Configure the verified support@skyeafricansupermarket.com mailbox and PHP mail on the hosting account; password resets and subscription confirmation use it.
5. Put the logo at assets/logo.png and product photos at assets/products/SKU.jpg (e.g. SKY-0001.jpg).
8. (Phone photos are big: .user.ini raises the upload limit to 12 MB per image. If uploads still fail, raise upload_max_filesize and post_max_size in cPanel > MultiPHP INI Editor.)
8. First admin login: username Admin, password Admin123. Admin Settings will require you to change both before using the dashboard.
9. Under Settings: free-delivery amount, shop WhatsApp number, top-of-site message.
10. Manage product categories under Admin > Categories. Category photos (optional): open a category in Admin > Categories and use Add picture (square works best). Without one the shop shows an icon.
11. SEO: after launch, add the site to Google Search Console and submit https://YOURDOMAIN/sitemap.xml. Claim and complete your Google Business Profile with the same address, phone number and opening hours as the website.
12. Optional: replace assets/og.jpg with a 1200x630 picture of the shop. It is the image shown when the site is shared on WhatsApp and social media.
