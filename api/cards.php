<?php require '../inc/layout.php';$s=array_slice(array_values(array_filter(array_map('trim',explode(',',$_GET['skus']??'')))),0,12);if(!$s)exit;
$st=db()->prepare("SELECT p.*,s.name sub,s.slug subslug,t.name top FROM products p JOIN categories s ON s.id=p.category_id LEFT JOIN categories t ON t.id=s.parent_id WHERE p.sku IN(".implode(',',array_fill(0,count($s),'?')).") AND p.status='active'");$st->execute($s);$rows=$st->fetchAll();
usort($rows,fn($a,$b)=>array_search($a['sku'],$s)<=>array_search($b['sku'],$s));foreach($rows as $p)card($p);
