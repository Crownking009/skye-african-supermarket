<?php require 'inc/db.php';header('Content-Type: application/xml; charset=utf-8');
$u=[['',null,'1.0']];foreach(['shop','recipes','blog','faq','delivery','about','contact'] as $x)$u[]=[$x,null,'0.7'];
foreach(db()->query('SELECT slug,updated_at FROM blog_posts WHERE active=1') as $p)$u[]=['blog/'.$p['slug'],$p['updated_at'],'0.6'];
foreach(db()->query('SELECT slug FROM categories') as $c)$u[]=[u_cat($c['slug']),null,'0.8'];
try{foreach(db()->query('SELECT slug FROM collections WHERE active=1') as $c)$u[]=[u_col($c['slug']),null,'0.6'];}catch(Exception $x){}
foreach(db()->query('SELECT slug FROM recipes WHERE active=1') as $c)$u[]=[u_rec($c['slug']),null,'0.6'];
foreach(db()->query("SELECT sku,name,created_at FROM products WHERE status='active'") as $p){$im=[];foreach(product_images($p['sku']) as $f)$im[]=abs_url(strtok($f,'?'));$u[]=[u_product($p),$p['created_at'],'0.9',$im];}
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
foreach($u as $x){echo '<url><loc>'.htmlspecialchars(abs_url($x[0])).'</loc>'.($x[1]?'<lastmod>'.date('Y-m-d',strtotime($x[1])).'</lastmod>':'').'<priority>'.$x[2].'</priority>';foreach($x[3]??[] as $i)echo '<image:image><image:loc>'.htmlspecialchars($i).'</image:loc></image:image>';echo '</url>';}
echo '</urlset>';
