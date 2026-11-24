<?php require 'inc/layout.php';$GLOBALS['seo']=['canonical'=>'delivery'];$a=seo_area();$fo=number_format(free_over(),2);$z=db()->query('SELECT * FROM delivery_zones ORDER BY fee')->fetchAll();$tr=bank();
head('Delivery and Collection | Skye African Supermarket '.$a,'Collect in store at '.shop_address().' or ask for delivery. See delivery zones, fees and free delivery over £'.number_format(free_over(),0).'.',crumbs([['Home',''],['Delivery & collection','delivery']]));?>
<main class="wrap1 txt"><nav class="crumb"><a href="./">Home</a> / Delivery &amp; collection</nav><h1 class="ph1">Delivery and collection</h1>
<h2>Collect in store</h2><p>Choose Collect in store at checkout and pick up from <b><?=e(shop_address())?></b>. <?=e(implode('. ',hours_lines()))?>.</p>
<h2>Delivery</h2><p>Enter your postcode at checkout to see your delivery fee. Delivery is free in our zones on orders over £<?=$fo?>.</p>
<?php if($z): ?><table class="tb"><tr><th>Zone</th><th>Postcodes</th><th>Fee</th><th>Minimum order</th></tr><?php foreach($z as $r): ?><tr><td><?=e($r['name'])?></td><td><?=e($r['codes'])?></td><td><?=money($r['fee'])?></td><td><?=money($r['min_order'])?></td></tr><?php endforeach; ?></table><?php endif; ?>
<p>Not in one of these areas? Message us on <a href="https://wa.me/<?=e(wa_number())?>"><?=e(wa_display())?></a> and we will tell you if we can deliver to you.</p>
<h2>Payment</h2><p><?=$tr?'Pay by bank transfer and send your receipt on WhatsApp to confirm your order, or pay on collection or delivery.':'Pay on collection or delivery.'?></p>
<p><a class="b1x" href="shop">Start shopping</a></p></main><?php foot();
