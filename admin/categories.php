<?php
require '_layout.php';require_once __DIR__.'/../inc/img.php';$db=db();$cdir=__DIR__.'/../assets/categories/';if(!is_dir($cdir))@mkdir($cdir,0755,true);$id=(int)($_GET['id']??$_GET['edit']??0);$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);
 if(isset($_POST['delete'])){
  $s=$db->prepare('SELECT (SELECT COUNT(*) FROM products WHERE category_id=?)+(SELECT COUNT(*) FROM product_category_map WHERE category_id=?)');$s->execute([$id,$id]);$used=(int)$s->fetchColumn();
  $s=$db->prepare('SELECT COUNT(*) FROM categories WHERE parent_id=?');$s->execute([$id]);$children=(int)$s->fetchColumn();
  if($used||$children)$error='This category still contains products or subcategories. Move or remove them before deleting it.';
  else{$db->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);@unlink($cdir.$id.'.jpg');header('Location: categories.php?deleted=1');exit;}
 }
 else{
  $name=trim((string)($_POST['name']??''));$parent=(int)($_POST['parent_id']??0)?:null;
  if($name==='')$error='Enter a category name.';
  elseif($parent===$id&&$id)$error='A category cannot be its own parent.';
  elseif(($ck=img_ok($_FILES['photo']??[]))!==true&&is_string($ck))$error='Picture: '.$ck;
  else{
   if($parent){$s=$db->prepare('SELECT parent_id FROM categories WHERE id=?');$s->execute([$parent]);if($s->fetchColumn())$error='Choose a top-level aisle as the parent.';}
   if(!$error){$st=$db->prepare('SELECT name FROM categories WHERE id=?');$st->execute([$parent]);$parentName=$parent?$st->fetchColumn():'';$base=slug($parent?$parentName.' '.$name:$name);$newSlug=$base;$n=2;
    while(true){$check=$db->prepare('SELECT id FROM categories WHERE slug=? AND id<>?');$check->execute([$newSlug,$id]);if(!$check->fetchColumn())break;$newSlug=$base.'-'.$n++;}
    if($id)$db->prepare('UPDATE categories SET name=?,slug=?,parent_id=?,sort=? WHERE id=?')->execute([$name,$newSlug,$parent,(int)($_POST['sort']??0),$id]);
    else{$db->prepare('INSERT INTO categories(name,slug,parent_id,sort) VALUES(?,?,?,?)')->execute([$name,$newSlug,$parent,(int)($_POST['sort']??0)]);$id=(int)$db->lastInsertId();}
    if($ck===true)img_save($_FILES['photo'],$cdir.$id.'.jpg',700);elseif(isset($_POST['rmimg']))@unlink($cdir.$id.'.jpg');
    header('Location: categories.php?saved=1');exit;
   }
  }
 }
}
if(isset($_GET['edit'])){
 $s=$db->prepare('SELECT * FROM categories WHERE id=?');$s->execute([$id]);$category=$s->fetch()?:['id'=>0,'name'=>'','parent_id'=>null,'sort'=>0];
 if($_SERVER['REQUEST_METHOD']==='POST')$category=array_merge($category,['name'=>$name??'','parent_id'=>$parent??null,'sort'=>(int)($_POST['sort']??0)]);
 $tops=$db->query('SELECT id,name FROM categories WHERE parent_id IS NULL ORDER BY sort,name')->fetchAll();ahead($category['id']?'Edit category':'Add category','categories.php');
 ?><?php if($error): ?><p class="bad"><?=e($error)?></p><?php endif; ?><form method="post" class="fm" enctype="multipart/form-data"><?=csrf()?><input type="hidden" name="id" value="<?=$category['id']?>"><label>Category name<input name="name" required maxlength="120" value="<?=e($category['name'])?>"></label><label>Parent aisle<select name="parent_id"><option value="0">Top-level aisle</option><?php foreach($tops as $top)if($top['id']!=$id): ?><option value="<?=$top['id']?>" <?=$category['parent_id']==$top['id']?'selected':''?>><?=e($top['name'])?></option><?php endif; ?></select></label><label>Sort order<input type="number" name="sort" value="<?=e($category['sort'])?>"></label><?php $cp=$category['id']?$cdir.$category['id'].'.jpg':'';$legacy=$category['id']?cat_img($category):''; ?><div class="fld">Category picture (optional, square works best)<?php if($cp&&is_file($cp)): ?><div><img class="th2" style="width:90px;height:90px;border-radius:50%;object-fit:cover" src="../assets/categories/<?=$category['id']?>.jpg?<?=filemtime($cp)?>" alt=""></div><label class="chk"><input type="checkbox" name="rmimg"> Remove picture</label><?php elseif($legacy): ?><div><img class="th2" style="width:90px;height:90px;border-radius:50%;object-fit:cover" src="../<?=e($legacy)?>" alt=""></div><small class="mut">Using a picture uploaded by file name. Upload a new one here to replace it.</small><?php endif; ?><label class="pick"><?=($cp&&is_file($cp))||$legacy?'Replace picture':'Add picture'?><input type="file" name="photo" accept="image/*"></label></div><button class="btn">Save category</button> <a href="categories.php">Cancel</a><?php if($category['id']): ?> <button class="btn ghost" name="delete" value="1" onclick="return confirm('Delete this empty category?')">Delete</button><?php endif; ?></form><?php afoot();exit;
}
ahead('Categories','categories.php');?><?php if(isset($_GET['saved'])): ?><p class="okm">Category saved.</p><?php endif; ?><?php if(isset($_GET['deleted'])): ?><p class="okm">Category deleted.</p><?php endif; ?><p>Products can be assigned to more than one aisle. Change the primary aisle and additional category placements from the product editor.</p><p><a class="btn" href="?edit=0">+ Add category</a></p><table class="tb"><tr><th>Category</th><th>Products</th><th>Subcategories</th></tr><?php foreach($db->query('SELECT c.*,p.name parent,(SELECT COUNT(DISTINCT product_id) FROM product_category_map WHERE category_id=c.id) products,(SELECT COUNT(*) FROM categories x WHERE x.parent_id=c.id) children FROM categories c LEFT JOIN categories p ON p.id=c.parent_id ORDER BY COALESCE(p.sort,c.sort),COALESCE(p.name,c.name),c.name') as $c): ?><tr><td><a href="?edit=<?=$c['id']?>"><?=e($c['parent']?'— '.$c['name']:$c['name'])?></a><?php if($c['parent']): ?><small class="mut"> · <?=e($c['parent'])?></small><?php endif; ?></td><td><?=$c['products']?></td><td><?=$c['children']?></td></tr><?php endforeach; ?></table><?php afoot();
