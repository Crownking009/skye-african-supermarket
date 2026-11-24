<?php require '../inc/cart.php';header('Content-Type: application/json');
$in=json_decode(file_get_contents('php://input'),true)?:[];$r=priced($in['cart']??[]);$d=delivery($in['method']??'collect',$in['postcode']??'',$r['subtotal']);$pr=promo($in['promo']??'',$r['subtotal']);
echo json_encode($r+['delivery'=>$d,'promo'=>$pr,'total'=>round($r['subtotal']-$pr['discount']+$d['fee'],2),'free_over'=>free_over()]);
