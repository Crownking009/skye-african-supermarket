<?php require '_layout.php';$db=db();ahead('Dashboard','index.php');
$one=fn($q)=>$db->query($q)->fetch(PDO::FETCH_NUM);
[$tn,$tt]=$one("SELECT COUNT(*),COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status<>'cancelled'");
[,$wk]=$one("SELECT 0,COALESCE(SUM(total),0) FROM orders WHERE created_at>=CURDATE()-INTERVAL 6 DAY AND status<>'cancelled'");
[,$mo]=$one("SELECT 0,COALESCE(SUM(total),0) FROM orders WHERE created_at>=DATE_FORMAT(CURDATE(),'%Y-%m-01') AND status<>'cancelled'");
[$nw]=$one("SELECT COUNT(*) FROM orders WHERE status='new'");
$days=[];foreach($db->query("SELECT DATE(created_at) d,SUM(total) t FROM orders WHERE created_at>=CURDATE()-INTERVAL 6 DAY AND status<>'cancelled' GROUP BY d") as $r)$days[$r['d']]=(float)$r['t'];
$series=[];for($i=6;$i>=0;$i--){$d=date('Y-m-d',strtotime("-$i day"));$series[$d]=$days[$d]??0;}$mx=max(1,max($series));
?><div class="cards"><div class="kc"><small>New orders waiting</small><b><?=$nw?></b></div><div class="kc"><small>Today</small><b><?=money($tt)?></b><small><?=$tn?> orders</small></div><div class="kc"><small>Last 7 days</small><b><?=money($wk)?></b></div><div class="kc"><small>This month</small><b><?=money($mo)?></b></div></div>
<div class="two"><section class="pn"><h3>Sales, last 7 days</h3><div class="bars"><?php foreach($series as $d=>$v): ?><div><i style="height:<?=round($v/$mx*100)?>%" title="<?=money($v)?>"></i><small><?=date('D',strtotime($d))?></small></div><?php endforeach; ?></div></section>
<section class="pn"><h3>Low stock (5 or fewer)</h3><table><?php foreach($db->query("SELECT sku,name,stock FROM products WHERE status='active' AND stock<=5 ORDER BY stock,name LIMIT 8") as $r): ?><tr><td><a href="product.php?sku=<?=e($r['sku'])?>"><?=e($r['name'])?></a></td><td class="r"><b class="<?=$r['stock']?'':'bad'?>"><?=$r['stock']?'':'Out'?><?=$r['stock']?:''?></b></td></tr><?php endforeach; ?></table></section></div>
<div class="two"><section class="pn"><h3>Recent orders</h3><table><?php foreach($db->query("SELECT id,ref,name,total,status FROM orders ORDER BY id DESC LIMIT 8") as $r): ?><tr><td><a href="orders.php?id=<?=$r['id']?>"><?=e($r['ref'])?></a></td><td><?=e($r['name'])?></td><td><?=money($r['total'])?></td><td><?=badge($r['status'])?></td></tr><?php endforeach; ?></table></section>
<section class="pn"><h3>Top products, last 30 days</h3><table><?php foreach($db->query("SELECT i.name,SUM(i.line_total) t,COUNT(*) n FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.created_at>=CURDATE()-INTERVAL 30 DAY AND o.status<>'cancelled' GROUP BY i.name ORDER BY t DESC LIMIT 6") as $r): ?><tr><td><?=e($r['name'])?></td><td><?=$r['n']?> orders</td><td class="r"><?=money($r['t'])?></td></tr><?php endforeach; ?></table></section></div>
<?php afoot();
