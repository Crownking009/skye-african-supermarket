<?php require '../inc/db.php';header('Content-Type: application/json');
$q=trim($_GET['q']??'');if(mb_strlen($q)<2){echo '[]';exit;}
$p=array_map(fn($r)=>['n'=>$r['name'],'c'=>$r['sub'],'p'=>money($r['base_price']),'u'=>u_product($r)],find($q,[],6));
$st=db()->prepare('SELECT name,slug FROM categories WHERE name LIKE ? LIMIT 4');$st->execute(["%$q%"]);
echo json_encode(['products'=>$p,'cats'=>array_map(fn($c)=>['n'=>$c['name'],'u'=>'shop/'.$c['slug']],$st->fetchAll())]);
