<?php require '_layout.php';$db=db();
if($_SERVER['REQUEST_METHOD']==='POST'){$d=$_POST;$id=(int)($d['id']??0);
 if(isset($d['del'])&&$id)$db->prepare('DELETE FROM delivery_zones WHERE id=?')->execute([$id]);
 elseif(trim($d['name'])!==''){$v=[trim($d['name']),strtoupper(preg_replace('/\s+/','',$d['codes'])),(float)$d['fee'],(float)$d['min_order']];
  if($id)$db->prepare('UPDATE delivery_zones SET name=?,codes=?,fee=?,min_order=? WHERE id=?')->execute(array_merge($v,[$id]));else $db->prepare('INSERT INTO delivery_zones(name,codes,fee,min_order) VALUES(?,?,?,?)')->execute($v);}
 header('Location: zones.php?ok=1');exit;}
ahead('Delivery zones','zones.php');?><?=isset($_GET['ok'])?'<p class="okm">Saved</p>':''?>
<p>List the outward postcodes each zone covers, separated by commas (for example <code>SE1,SE15</code>). Free delivery above £<?=number_format(free_over(),2)?> is set under Settings. Postcodes not listed show "To be confirmed" at checkout.</p>
<?php foreach(array_merge($db->query('SELECT * FROM delivery_zones ORDER BY fee')->fetchAll(),[['id'=>0,'name'=>'','codes'=>'','fee'=>'','min_order'=>'']]) as $z): ?>
<form method="post" class="zr"><?=csrf()?><input type="hidden" name="id" value="<?=$z['id']?>"><input name="name" placeholder="<?=$z['id']?'':'New zone name'?>" value="<?=e($z['name'])?>"><input name="codes" placeholder="Postcodes e.g. SE1,SE15" value="<?=e($z['codes'])?>"><input name="fee" type="number" step="0.01" placeholder="Fee £" value="<?=e($z['fee'])?>"><input name="min_order" type="number" step="0.01" placeholder="Min order £" value="<?=e($z['min_order'])?>"><button class="btn"><?=$z['id']?'Save':'Add zone'?></button><?php if($z['id']): ?><button class="btn ghost" name="del" value="1" onclick="return confirm('Delete this zone?')">Delete</button><?php endif; ?></form><?php endforeach; afoot();
