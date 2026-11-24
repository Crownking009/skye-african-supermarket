<?php require 'inc/layout.php';$GLOBALS['seo']=['canonical'=>'faq'];$a=seo_area();$fo=number_format(free_over(),0);$tr=bank();
$q=[['Where is Skye African Supermarket?','We are at '.shop_address().' in '.$a.'. You can get directions from the Visit us page.'],
['What are your opening hours?',implode('. ',hours_lines()).'.'],
['Can I order online and collect in store?','Yes. Add your items to the basket, choose Collect in store at checkout and send your order to us on WhatsApp. We will confirm and have it ready for you at '.shop_address().'.'],
['Do you deliver?','We deliver to selected postcode areas. Enter your postcode at checkout to see your delivery fee, and delivery is free in our zones on orders over £'.$fo.'. If your postcode is not listed, message us on WhatsApp and we will confirm if we can deliver to you.'],
['How do I pay?',($tr?'You can pay by bank transfer and send your payment receipt on WhatsApp so we can confirm your order, or pay on collection or delivery.':'You can pay on collection or delivery.')],
['What African food do you sell?','We stock fresh produce such as yam and plantain, rice, garri and swallow flours, beans, spices and soup ingredients, palm oil, stockfish and dried fish, frozen foods, snacks and drinks.'],
['Do you sell hair and beauty products?','Yes. Our beauty aisle includes human hair bundles, closures, frontals and wigs, braiding hair, hair care, shea butter, black soap and skin care, so you can shop groceries and beauty in one basket.'],
['What happens if something is out of stock?','At checkout you can choose to substitute similar items, have us call you first, or leave the item out.'],
['Can I order on WhatsApp?','Yes. Message us on '.wa_display().' or place your order on the website and send it to us on WhatsApp to confirm.']];
$ld=crumbs([['Home',''],['FAQ','faq']]).jsonld(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array_map(fn($x)=>['@type'=>'Question','name'=>$x[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>$x[1]]],$q)]);
head('FAQ: Delivery, Collection and Payment | Skye African Supermarket','Answers about collection, delivery, payment, opening hours and products at Skye African Supermarket in '.$a.'.',$ld);?>
<main class="wrap1 txt"><nav class="crumb"><a href="./">Home</a> / FAQ</nav><h1 class="ph1">Frequently asked questions</h1><?php foreach($q as $x): ?><details class="fq"><summary><?=e($x[0])?></summary><p><?=e($x[1])?></p></details><?php endforeach; ?><p><a class="b1x" href="contact">Visit us</a></p></main><?php foot();
