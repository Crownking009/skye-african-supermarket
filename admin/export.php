<?php require '_layout.php';$db=db();$fr=$_GET['from']??date('Y-m-d',strtotime('-29 day'));$to=$_GET['to']??date('Y-m-d');
function cs($v){$v=(string)$v;if($v!==''&&strpos('=+-@',$v[0])!==false)$v="'".$v;return '"'.str_replace('"','""',$v).'"';}
$it=($_GET['t']??'')=='items';header('Content-Type: text/csv');header('Content-Disposition: attachment; filename=skye-'.($it?'items':'orders').'-'.$fr.'-to-'.$to.'.csv');
$sql=$it?'SELECT o.ref,i.sku,i.name,i.variant,i.qty,i.unit,i.unit_price,i.line_total FROM order_items i JOIN orders o ON o.id=i.order_id':'SELECT o.ref,o.created_at,o.name,o.phone,o.method,o.zone,o.postcode,o.status,o.subtotal,o.discount,o.delivery_fee,o.total FROM orders o';
$s=$db->prepare($sql." WHERE o.created_at>=? AND o.created_at<DATE_ADD(?,INTERVAL 1 DAY) ORDER BY o.id");$s->execute([$fr,$to]);$first=true;
foreach($s as $r){if($first){echo implode(',',array_keys($r))."\n";$first=false;}echo implode(',',array_map('cs',$r))."\n";}
