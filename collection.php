<?php require 'inc/layout.php';
$s=db()->prepare('SELECT * FROM collections WHERE slug=? AND active=1');$s->execute([$_GET['slug']??'']);$c=$s->fetch();
if(!$c){http_response_code(404);head('Collection not found');echo '<main class="wrap1"><h1 class="ph1">Collection not found</h1><a class="b1x" href="shop">Back to the shop</a></main>';foot();exit;}
$st=db()->prepare("SELECT p.*,s.name sub,s.slug subslug,t.name top FROM collection_items i JOIN products p ON p.id=i.product_id JOIN categories s ON s.id=p.category_id LEFT JOIN categories t ON t.id=s.parent_id WHERE i.collection_id=? AND p.status='active' ORDER BY i.sort,i.id");$st->execute([$c['id']]);$rows=$st->fetchAll();
$GLOBALS['seo']=['canonical'=>u_col($c['slug'])];head($c['title'].' | Shop the Collection | Skye African Supermarket',($c['subtitle']?:$c['title']).' at Skye African Supermarket in '.seo_area().'. Collect in store or arrange delivery.',crumbs([['Home',''],[$c['title'],u_col($c['slug'])]]));?>
<main class="wrap1"><nav class="crumb"><a href="./">Home</a> / <?=e($c['title'])?></nav><header class="shh"><h1><?=e($c['title'])?></h1><?php if($c['subtitle']): ?><p><?=e($c['subtitle'])?></p><?php endif; ?></header>
<div class="grid"><?php foreach($rows as $p)card($p); ?></div><?php if(!$rows)echo '<p>No products in this collection yet.</p>'; ?></main><?php foot();
