<?php require_once __DIR__.'/db.php';
const SLOTS=['Morning (9am-12pm)','Afternoon (12pm-4pm)','Evening (4pm-7pm)'];
function priced($cart){$lines=[];$sub=0;$age=false;$pdo=db();
 foreach((array)$cart as $c){$st=$pdo->prepare("SELECT * FROM products WHERE sku=? AND status='active'");$st->execute([(string)($c['sku']??'')]);$p=$st->fetch();if(!$p)continue;
  $label=(string)($c['variant']??'');$price=(float)$p['base_price'];
  if($p['sold_by_type']=='variant'){$v=$pdo->prepare('SELECT * FROM product_variants WHERE product_id=? ORDER BY (label=?) DESC,sort LIMIT 1');$v->execute([$p['id'],$label]);if($r=$v->fetch()){$label=$r['label'];$price=(float)$r['price'];}}else $label='';
  $step=(float)$p['qty_step']?:1;$min=(float)$p['min_qty']?:1;$q=min(200,max($min,round(((float)($c['qty']??1))/$step)*$step));$tot=round($price*$q,2);$sub+=$tot;if($p['age_restricted'])$age=true;
  $lines[]=['key'=>(string)($c['key']??$p['sku']),'sku'=>$p['sku'],'name'=>$p['name'],'variant'=>$label,'qty'=>$q,'step'=>$step,'min'=>$min,'unit'=>$p['sold_by_type']=='weight'?$p['unit_label']:'','unit_price'=>$price,'total'=>$tot,'stock'=>(int)$p['stock'],'img'=>file_exists(__DIR__.'/../assets/products/'.$p['sku'].'.jpg')?'assets/products/'.$p['sku'].'.jpg':'','ini'=>mb_strtoupper(mb_substr($p['name'],0,1))];}
 return ['lines'=>$lines,'subtotal'=>round($sub,2),'age'=>$age];}
function delivery($method,$postcode,$sub){
 if($method!='delivery')return ['ok'=>true,'fee'=>0,'zone'=>'Collection','note'=>''];
 $pc=strtoupper(preg_replace('/\s+/','',$postcode));$out=strlen($pc)>3?substr($pc,0,-3):$pc;
 foreach(db()->query('SELECT * FROM delivery_zones') as $z){if(in_array($out,array_map('trim',explode(',',strtoupper($z['codes']))))){
  if($sub<$z['min_order'])return ['ok'=>false,'fee'=>0,'zone'=>$z['name'],'note'=>'Minimum order for '.$z['name'].' is '.money($z['min_order']).'. Add '.money($z['min_order']-$sub).' more, or choose collection.'];
  $fee=$sub>=free_over()?0:(float)$z['fee'];return ['ok'=>true,'fee'=>$fee,'zone'=>$z['name'],'note'=>$z['name'].($fee?'':': free delivery')];}}
 return ['ok'=>true,'fee'=>0,'zone'=>'To be confirmed','note'=>$pc?'We will confirm your delivery fee with you on WhatsApp.':''];}
function promo($code,$sub){$code=strtoupper(trim((string)$code));if($code==='')return ['ok'=>true,'discount'=>0,'msg'=>'','code'=>''];
 $s=db()->prepare('SELECT * FROM promo_codes WHERE code=? AND active=1 AND (expires IS NULL OR expires>=CURDATE())');$s->execute([$code]);$p=$s->fetch();
 if(!$p)return ['ok'=>false,'discount'=>0,'msg'=>'That code is not valid or has expired.','code'=>$code];
 if($sub<$p['min_order'])return ['ok'=>false,'discount'=>0,'msg'=>'Spend '.money($p['min_order']).' or more to use this code.','code'=>$code];
 $d=$p['type']=='percent'?round($sub*$p['value']/100,2):min($sub,(float)$p['value']);
 return ['ok'=>true,'discount'=>$d,'msg'=>$code.' applied: you save '.money($d),'code'=>$code];}
