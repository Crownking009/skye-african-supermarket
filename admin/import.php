<?php require '../inc/db.php';session_start();
if(empty($_SESSION['a'])||!in_array($_SESSION['role']??'',['owner','manager'])){header('Location: index.php');exit;}
$msg='';
if(!empty($_FILES['csv']['tmp_name'])){
 $h=fopen($_FILES['csv']['tmp_name'],'r');if(fread($h,3)!="\xEF\xBB\xBF")rewind($h);fgetcsv($h);
 $pdo=db();$pdo->beginTransaction();$n=0;$bad=0;
 $cat=function($name,$parent=null,$ptn='')use($pdo){$sl=slug($parent?$ptn.' '.$name:$name);$s=$pdo->prepare('SELECT id FROM categories WHERE slug=?');$s->execute([$sl]);if($id=$s->fetchColumn())return $id;
  $pdo->prepare('INSERT INTO categories(name,slug,parent_id) VALUES(?,?,?)')->execute([$name,$sl,$parent]);return $pdo->lastInsertId();};
 while(($r=fgetcsv($h))!==false){if(count($r)<15||!trim($r[0])){$bad++;continue;}
  [$sku,$name,$c,$sc,$or,$ty,$un,$pr,$va,$mn,$st,$ag,$sk,$kw,$stt]=$r;$t=$cat($c);$sid=$cat($sc,$t,$c);
  $pdo->prepare('INSERT INTO products(sku,name,category_id,origin,sold_by_type,unit_label,base_price,min_qty,qty_step,age_restricted,stock,search_keywords,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),category_id=VALUES(category_id),origin=VALUES(origin),sold_by_type=VALUES(sold_by_type),unit_label=VALUES(unit_label),base_price=VALUES(base_price),min_qty=VALUES(min_qty),qty_step=VALUES(qty_step),age_restricted=VALUES(age_restricted),stock=VALUES(stock),search_keywords=VALUES(search_keywords),status=VALUES(status)')
  ->execute([$sku,$name,$sid,$or,$ty,$un,(float)$pr,(float)$mn?:1,(float)$st?:1,strcasecmp($ag,'yes')==0?1:0,(int)$sk,$kw,$stt=='hidden'?'hidden':'active']);
  $q=$pdo->prepare('SELECT id FROM products WHERE sku=?');$q->execute([$sku]);$id=$q->fetchColumn();
  $pdo->prepare('DELETE FROM product_variants WHERE product_id=?')->execute([$id]);
  if(trim($va))foreach(explode(',',$va) as $i=>$v){[$l,$p]=array_pad(explode('=',$v,2),2,'');if(trim($l)!==''&&is_numeric($p))$pdo->prepare('INSERT INTO product_variants(product_id,label,price,sort) VALUES(?,?,?,?)')->execute([$id,trim($l),$p,$i]);}
  $n++;}
 $pdo->commit();$msg="Imported $n products".($bad?", skipped $bad bad rows":'').'.';}
?><!doctype html><meta charset=utf-8><body style="font:16px sans-serif;max-width:520px;margin:12vh auto;padding:0 16px"><h2>Import products</h2><p>Upload the CSV in the same column order as skye_products_seed.csv. Existing SKUs are updated, new SKUs are added.</p><?php if($msg)echo '<p style="color:#0E8A4B"><b>'.e($msg).'</b></p>'; ?><form method=post enctype=multipart/form-data><input type=file name=csv accept=.csv required> <button style="padding:8px 16px">Import</button></form><p><a href="index.php">Back to dashboard</a></p>
