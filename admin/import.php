<?php require '../inc/db.php';session_start();
if(empty($_SESSION['a'])||!in_array($_SESSION['role']??'',['owner','manager'])){header('Location: index.php');exit;}
$msg='';$err='';
if(!empty($_FILES['file']['tmp_name'])){
 $pdo=db();$ext=strtolower(pathinfo($_FILES['file']['name']??'',PATHINFO_EXTENSION));$peek=file_get_contents($_FILES['file']['tmp_name']);$isJson=$ext==='json'||(in_array($ext,['txt',''])&&substr(ltrim((string)$peek),0,1)==='[');
 $cat=function($name,$parent=null,$parentName='')use($pdo){$name=trim((string)$name);$sl=slug($parent?$parentName.' '.$name:$name);$s=$pdo->prepare('SELECT id FROM categories WHERE slug=?');$s->execute([$sl]);if($id=$s->fetchColumn())return (int)$id;$pdo->prepare('INSERT INTO categories(name,slug,parent_id) VALUES(?,?,?)')->execute([$name,$sl,$parent]);return (int)$pdo->lastInsertId();};
 try{
  $pdo->beginTransaction();
  if($isJson){
   $items=json_decode((string)$peek,true,512,JSON_THROW_ON_ERROR);
   if(!is_array($items)||!isset($items[0]))throw new RuntimeException('The JSON file must contain a list of products.');
   $seen=[];$n=0;
   foreach($items as $item){
    $num=(int)($item['id']??0);$name=trim((string)($item['name']??''));$price=$item['price']??null;$rawLabels=is_array($item['categories']??null)?$item['categories']:[];$labels=array_values(array_unique(array_filter(array_map(static fn($x)=>trim((string)$x),$rawLabels),static fn($x)=>$x!=='')));
    if($num<1||$name===''||!is_numeric($price)||$price<0||isset($seen[$num]))throw new RuntimeException('Invalid or duplicate product ID, name, or price in the JSON file.');
    $seen[$num]=true;$topName=$labels[0]??'Uncategorized';$subName=$labels[1]??'General';$topId=$cat($topName);$subId=$cat($subName,$topId,$topName);$sku='CAT-'.str_pad((string)$num,4,'0',STR_PAD_LEFT);
    $keywords=strtolower($name.' '.implode(' ',$labels));
    $pdo->prepare("INSERT INTO products(sku,name,category_id,origin,sold_by_type,unit_label,base_price,min_qty,qty_step,age_restricted,stock,search_keywords,status) VALUES(?,?,?,NULL,'pack','item',?,1,1,0,0,?,'active') ON DUPLICATE KEY UPDATE name=VALUES(name),category_id=VALUES(category_id),base_price=VALUES(base_price),search_keywords=VALUES(search_keywords),status='active'")->execute([$sku,$name,$subId,(float)$price,$keywords]);
    $n++;
   }
   $pdo->commit();$msg="Imported $n products from JSON. Assigned aisle categories and the numbered product images when present.";
  }elseif($ext==='csv'){
   $h=fopen($_FILES['file']['tmp_name'],'r');if(!$h)throw new RuntimeException('The CSV file could not be read.');if(fread($h,3)!="\xEF\xBB\xBF")rewind($h);fgetcsv($h);$n=0;$bad=0;
   while(($r=fgetcsv($h))!==false){if(count($r)<15||!trim($r[0])){$bad++;continue;}
    [$sku,$name,$c,$sc,$or,$ty,$un,$pr,$va,$mn,$st,$ag,$stock,$kw,$status]=$r;$top=$cat($c);$sid=$cat($sc,$top,$c);
    $pdo->prepare('INSERT INTO products(sku,name,category_id,origin,sold_by_type,unit_label,base_price,min_qty,qty_step,age_restricted,stock,search_keywords,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),category_id=VALUES(category_id),origin=VALUES(origin),sold_by_type=VALUES(sold_by_type),unit_label=VALUES(unit_label),base_price=VALUES(base_price),min_qty=VALUES(min_qty),qty_step=VALUES(qty_step),age_restricted=VALUES(age_restricted),stock=VALUES(stock),search_keywords=VALUES(search_keywords),status=VALUES(status)')->execute([$sku,$name,$sid,$or,$ty,$un,(float)$pr,(float)$mn?:1,(float)$st?:1,strcasecmp($ag,'yes')==0?1:0,(int)$stock,$kw,$status==='hidden'?'hidden':'active']);
    $q=$pdo->prepare('SELECT id FROM products WHERE sku=?');$q->execute([$sku]);$id=$q->fetchColumn();$pdo->prepare('DELETE FROM product_variants WHERE product_id=?')->execute([$id]);
    if(trim($va))foreach(explode(',',$va) as $i=>$v){[$label,$variantPrice]=array_pad(explode('=',$v,2),2,'');if(trim($label)!==''&&is_numeric($variantPrice))$pdo->prepare('INSERT INTO product_variants(product_id,label,price,sort) VALUES(?,?,?,?)')->execute([$id,trim($label),$variantPrice,$i]);}
    $n++;
   }
   fclose($h);$pdo->commit();$msg='Imported '.$n.' products'.($bad?', skipped '.$bad.' bad rows':'').'.';
  }else throw new RuntimeException('Choose a JSON product list or the existing CSV format.');
 }catch(Throwable $x){if($pdo->inTransaction())$pdo->rollBack();$err=$x instanceof JsonException?'The JSON file is not valid.':$x->getMessage();}
}
?><!doctype html><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1"><link rel=stylesheet href="../assets/admin.css"><body style="font:16px sans-serif;max-width:560px;margin:10vh auto;padding:0 16px"><h2>Import products</h2><p>Upload the store product JSON list or the existing CSV format. JSON product IDs become CAT-0001, CAT-0002, and so on. The first category becomes its aisle and the second becomes its subcategory. Other category names are retained as searchable keywords.</p><p>For numbered photos, place files such as <code>product-1.png</code> through <code>product-812.png</code> in <code>assets/products/</code>. They are matched to the JSON product IDs. Initial stock is set to zero because the source list does not include stock counts.</p><?php if($msg)echo '<p class="okm">'.e($msg).'</p>';if($err)echo '<p class="bad">'.e($err).'</p>'; ?><form method=post enctype=multipart/form-data><input type=file name=file accept=".json,.txt,.csv,application/json,text/plain,text/csv" required> <button class=btn>Import</button></form><p><a href="index.php">Back to dashboard</a></p></body>
