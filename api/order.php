<?php require '../inc/cart.php';session_start();header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true)?:[];
function bad($m){http_response_code(422);echo json_encode(['ok'=>false,'error'=>$m]);exit;}
ensure_payment_col();$_SESSION['ot']=array_values(array_filter($_SESSION['ot']??[],fn($t)=>$t>time()-3600));if(count($_SESSION['ot'])>=6)bad('Too many orders from this device in the last hour. Please message us on WhatsApp.');
$r=priced($in['cart']??[]);if(!$r['lines'])bad('Your basket is empty.');
foreach($r['lines'] as $l){if($l['stock']<=0)bad($l['name'].' is out of stock. Please remove it from your basket.');if($l['qty']>$l['stock'])bad('Only '.$l['stock'].' of '.$l['name'].' left. Please lower the quantity.');}
$name=trim($in['name']??'');$phone=trim($in['phone']??'');$m=($in['method']??'')=='delivery'?'delivery':'collect';
if(mb_strlen($name)<2)bad('Please enter your name.');if(strlen(preg_replace('/\D/','',$phone))<7)bad('Please enter a valid phone number.');
if($m=='delivery'&&(trim($in['address']??'')==''||trim($in['postcode']??'')==''))bad('Please enter your delivery address and postcode.');
if($r['age']&&empty($in['age_ok']))bad('Please confirm you are 18 or over to buy age-restricted items.');
$d=delivery($m,$in['postcode']??'',$r['subtotal']);if(!$d['ok'])bad($d['note']);
$pay=(($in['payment']??'')=='transfer'&&bank())?'transfer':'cash';$pr=promo($in['promo']??'',$r['subtotal']);if(!$pr['ok'])bad($pr['msg']);$total=round($r['subtotal']-$pr['discount']+$d['fee'],2);$ref='SKY-'.strtoupper(bin2hex(random_bytes(4)));
$sub=in_array($in['sub']??'',['substitute','call','none'])?$in['sub']:'substitute';$date=preg_match('/^\d{4}-\d{2}-\d{2}$/',$in['date']??'')?$in['date']:null;
$pdo=db();$pdo->beginTransaction();
$pdo->prepare('INSERT INTO orders(ref,name,phone,email,method,address,town,postcode,zone,slot_date,slot_time,substitution,notes,subtotal,delivery_fee,total,promo_code,discount,payment) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([$ref,$name,$phone,trim($in['email']??''),$m,trim($in['address']??''),trim($in['town']??''),strtoupper(trim($in['postcode']??'')),$d['zone'],$date,mb_substr($in['slot']??'',0,40),$sub,mb_substr(trim($in['notes']??''),0,1000),$r['subtotal'],$d['fee'],$total,$pr['code']?:null,$pr['discount'],$pay]);
$id=$pdo->lastInsertId();
foreach($r['lines'] as $l)$pdo->prepare('INSERT INTO order_items(order_id,sku,name,variant,qty,unit,unit_price,line_total) VALUES(?,?,?,?,?,?,?,?)')->execute([$id,$l['sku'],$l['name'],$l['variant'],$l['qty'],$l['unit'],$l['unit_price'],$l['total']]);
foreach($r['lines'] as $l)$pdo->prepare('UPDATE products SET stock=GREATEST(0,stock-?) WHERE sku=?')->execute([(int)ceil($l['qty']),$l['sku']]);
$pdo->commit();$_SESSION['ref']=$ref;$_SESSION['ot'][]=time();echo json_encode(['ok'=>true,'ref'=>$ref]);
