<?php require 'inc/layout.php';
$q=trim($_GET['q']??'');$cat=$_GET['cat']??'';$pg=max(1,(int)($_GET['p']??1));$PP=24;
$f=['cat'=>$cat,'origin'=>$_GET['origin']??'','max'=>$_GET['max']??'','sort'=>$_GET['sort']??($q?'':'pop'),'offset'=>($pg-1)*$PP];
$rows=find($q,$f,$PP);$total=(int)($GLOBALS['total']??0);$pages=max(1,(int)ceil($total/$PP));
$all=db()->query('SELECT * FROM categories ORDER BY parent_id IS NOT NULL,sort,name')->fetchAll();$tops=[];$subs=[];$cn='';
foreach($all as $c){if($c['parent_id'])$subs[$c['parent_id']][]=$c;else $tops[]=$c;if($c['slug']==$cat)$cn=$c['name'];}
$origins=db()->query('SELECT DISTINCT origin FROM products WHERE origin<>"" ORDER BY origin')->fetchAll(PDO::FETCH_COLUMN);
$EM=['fresh-produce'=>'🥬','meat-fish-poultry'=>'🍖','rice-grains-flours'=>'🌾','spices-soup-ingredients'=>'🌶️','oils-sauces-condiments'=>'🫙','snacks-sweets'=>'🍿','drinks'=>'🥤','bakery-dairy'=>'🍞','frozen-foods'=>'🧊','beauty-hair'=>'💇🏾','skin-care-body'=>'🧴','household-kitchen'=>'🍳','health-wellness'=>'🌿'];
$self=$cat?u_cat($cat):'shop';$qs=fn($x)=>$self.'?'.http_build_query(array_filter(array_diff_key(array_merge($_GET,$x),['cat'=>1]),fn($v)=>$v!==''&&$v!==null));
function hid($skip=''){foreach(['q','cat','origin','max','sort'] as $k)if($k!=$skip&&($_GET[$k]??'')!=='')echo '<input type="hidden" name="'.$k.'" value="'.e($_GET[$k]).'">';}
$from=$total?($pg-1)*$PP+1:0;$to=min($total,$pg*$PP);
$ni=($q!==''||$f['origin']!==''||$f['max']!==''||($_GET['sort']??'')!=='');
if($cat&&!$cn){http_response_code(404);$ni=true;}
$GLOBALS['seo']=['canonical'=>$self.($pg>1?'?p='.$pg:''),'noindex'=>$ni];$names=implode(', ',array_slice(array_column($rows,'name'),0,4));
$T=$q?'Search results for '.$q:($cn?$cn.' | Buy African '.$cn.' Online':'Shop African Groceries, Beauty & Hair Online');
$D=$cn?'Shop '.$cn.' at Skye African Supermarket in '.seo_area().($names?': '.$names.' and more':'').'. Collect in store or arrange delivery.':'Shop authentic African groceries, fresh produce, frozen foods and beauty & hair products. Collect in store or arrange delivery in '.seo_area().'.';
$LD=crumbs(array_merge([['Home','']],$cn?[['Shop','shop'],[$cn,$self]]:[['Shop','shop']]));
if($rows&&!$ni){$it=[];foreach($rows as $i=>$p)$it[]=['@type'=>'ListItem','position'=>$i+1,'url'=>abs_url(u_product($p)),'name'=>$p['name']];$LD.=jsonld(['@context'=>'https://schema.org','@type'=>'ItemList','itemListElement'=>$it]);}
head($T.' | Skye African Supermarket',$D,$LD);?>
<main class="shp"><header class="shh"><h1><?=$q?'Results for “'.e($q).'”':e($cn?:'Shop Your Favourites')?></h1><?php if(!$q&&!$cn): ?><p>Everything you need, all in one place.</p><?php endif; ?></header>
<div class="cr"><button class="ca l" type="button" aria-label="Previous">&#8249;</button><div class="car" id="car"><?php foreach($tops as $t): $im='assets/categories/'.$t['slug'].'.jpg'; ?><a class="ct" href="shop/<?=e($t['slug'])?>"><span class="ci"><span class="ring"><?php if(file_exists(__DIR__.'/'.$im)): ?><img src="<?=e($im)?>" alt="" loading="lazy"><?php else: ?><?=$EM[$t['slug']]??e(mb_substr($t['name'],0,1))?><?php endif; ?></span></span><b><?=e($t['name'])?> &rarr;</b></a><?php endforeach; ?></div><button class="ca r" type="button" aria-label="Next">&#8250;</button></div>
<div class="fbar"><button class="fbtn" id="fopen" type="button"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 7h16M7 12h10M10 17h4"/></svg>Filters</button></div>
<div id="fov" hidden></div>
<div class="shw"><aside class="side" id="fp"><div class="fph"><b>Filters</b><button type="button" id="fclose" aria-label="Close">×</button></div>
<form method="get"><?php hid('origin'); ?>
<label>Origin<select name="origin"><option value="">Any</option><?php foreach($origins as $o): ?><option <?=$f['origin']==$o?'selected':''?>><?=e($o)?></option><?php endforeach; ?></select></label>
<label>Max price (£)<input type="number" name="max" min="1" value="<?=e($f['max'])?>"></label><button class="co">Apply filters</button><a class="kp2" href="shop<?=$q?'?q='.urlencode($q):''?>">Clear all</a></form>
<h3>Aisles</h3><?php foreach($tops as $t): ?><details <?=($cat==$t['slug']||in_array($cat,array_column($subs[$t['id']]??[],'slug')))?'open':''?>><summary><?=e($t['name'])?></summary><?php foreach($subs[$t['id']]??[] as $s): ?><a href="shop/<?=e($s['slug'])?>" class="<?=$cat==$s['slug']?'on':''?>"><?=e($s['name'])?></a><?php endforeach; ?></details><?php endforeach; ?></aside>
<section><p class="cnt"><?=$total?'Showing '.$from.'–'.$to.' of '.$total.' result'.($total==1?'':'s'):'No results'?></p>
<form class="sortr" method="get"><?php hid('sort'); ?><select name="sort" onchange="this.form.submit()" aria-label="Sort"><?php $so=['pop'=>'Sort by popularity','new'=>'Sort by newest','name'=>'Name A to Z','price_asc'=>'Price: low to high','price_desc'=>'Price: high to low'];if($q)$so=[''=>'Best match']+$so;foreach($so as $k=>$v)echo '<option value="'.$k.'" '.($f['sort']===(string)$k?'selected':'').'>'.$v.'</option>'; ?></select>
<button type="button" class="vt on" data-v="grid" aria-label="Grid view"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="5" height="5" rx="1"/><rect x="10" y="3" width="5" height="5" rx="1"/><rect x="17" y="3" width="4" height="5" rx="1"/><rect x="3" y="10" width="5" height="5" rx="1"/><rect x="10" y="10" width="5" height="5" rx="1"/><rect x="17" y="10" width="4" height="5" rx="1"/><rect x="3" y="17" width="5" height="4" rx="1"/><rect x="10" y="17" width="5" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/></svg></button>
<button type="button" class="vt" data-v="list" aria-label="List view"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button></form>
<?php if(!$rows): ?><div class="empty"><p>Nothing matched. Try a shorter word, or browse an aisle.</p><?php foreach(array_slice($tops,0,6) as $t): ?><a href="shop/<?=e($t['slug'])?>"><?=e($t['name'])?></a> <?php endforeach; ?></div><?php endif; ?>
<?php if(!empty($GLOBALS['fuzzy'])&&$rows): ?><p class="note">No exact match, so these are the closest products.</p><?php endif; ?>
<div class="grid" id="pg"><?php foreach($rows as $p)card($p); ?></div>
<?php if($pages>1): ?><nav class="pager" aria-label="Pages"><?php if($pg>1): ?><a href="<?=e($qs(['p'=>$pg-1]))?>">&larr; Prev</a><?php endif; for($i=max(1,$pg-2);$i<=min($pages,$pg+2);$i++)echo $i==$pg?'<span class="cur">'.$i.'</span>':'<a href="'.e($qs(['p'=>$i])).'">'.$i.'</a>'; if($pg<$pages): ?><a href="<?=e($qs(['p'=>$pg+1]))?>">Next &rarr;</a><?php endif; ?></nav><?php endif; ?></section></div></main>
<?php foot(['assets/shop.js']);
