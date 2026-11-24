<?php require 'inc/layout.php';$GLOBALS['seo']=['canonical'=>'about'];$a=seo_area();
head('About Us | Skye African Supermarket '.$a,'Skye African Supermarket is your home for authentic African goods in '.$a.': groceries, fresh produce, frozen foods and beauty & hair products.',crumbs([['Home',''],['About us','about']]));
$tops=db()->query("SELECT name,slug FROM categories WHERE parent_id IS NULL ORDER BY id")->fetchAll();?>
<main class="wrap1 txt"><nav class="crumb"><a href="./">Home</a> / About us</nav><h1 class="ph1">About Skye African Supermarket</h1>
<p>Skye African Supermarket is your home for authentic African goods in <?=e($a)?>. We bring together the groceries, fresh produce and frozen favourites you look for, with a beauty and hair aisle, so you can do it all in one shop.</p>
<h2>What you will find</h2><p>Our aisles include <?php foreach($tops as $i=>$t)echo ($i?', ':'').'<a href="'.e(u_cat($t['slug'])).'">'.e($t['name']).'</a>'; ?>.</p>
<h2>Shop your way</h2><p>Order online and collect in store at <?=e(shop_address())?>, or ask for delivery to your postcode. You can confirm your order with us on WhatsApp at <a href="https://wa.me/<?=e(wa_number())?>"><?=e(wa_display())?></a>.</p>
<p><a class="b1x" href="shop">Start shopping</a> <a class="b1x" href="contact">Find the shop</a></p></main><?php foot();
