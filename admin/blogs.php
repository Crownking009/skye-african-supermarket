<?php
require '_layout.php';$db=db();$id=(int)($_GET['id']??0);$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);
 if(isset($_POST['delete'])){$db->prepare('DELETE FROM blog_posts WHERE id=?')->execute([$id]);header('Location: blogs.php?deleted=1');exit;}
 $title=trim((string)($_POST['title']??''));$excerpt=trim((string)($_POST['excerpt']??''));$content=trim((string)($_POST['content']??''));
 $rawSlug=trim((string)($_POST['slug']??''));$postSlug=slug($rawSlug!==''?$rawSlug:$title);$image=trim((string)($_POST['cover_image']??''));$active=isset($_POST['active'])?1:0;
 if($title===''||$excerpt===''||$content===''||$postSlug==='')$error='Add a title, URL slug, summary and article text before saving.';
 elseif($image!==''&&!preg_match('#^(https://|http://|assets/)[^\s<>"\']+$#i',$image))$error='Use an https image URL or a path beginning with assets/.';
 else{
  $check=$db->prepare('SELECT id FROM blog_posts WHERE slug=? AND id<>? LIMIT 1');$check->execute([$postSlug,$id]);
  if($check->fetchColumn())$error='That URL slug is already in use. Choose a different one.';
  else{
   if($id)$db->prepare('UPDATE blog_posts SET title=?,slug=?,excerpt=?,content=?,cover_image=?,active=? WHERE id=?')->execute([$title,$postSlug,$excerpt,$content,$image?:null,$active,$id]);
   else $db->prepare('INSERT INTO blog_posts(title,slug,excerpt,content,cover_image,active) VALUES(?,?,?,?,?,?)')->execute([$title,$postSlug,$excerpt,$content,$image?:null,$active]);
   header('Location: blogs.php?saved=1');exit;
  }
 }
}
if(isset($_GET['edit'])){
 $s=$db->prepare('SELECT * FROM blog_posts WHERE id=?');$s->execute([$id]);$post=$s->fetch()?:['id'=>0,'title'=>'','slug'=>'','excerpt'=>'','content'=>'','cover_image'=>'','active'=>1];
 if($_SERVER['REQUEST_METHOD']==='POST')$post=array_merge($post,['title'=>$title,'slug'=>$postSlug,'excerpt'=>$excerpt,'content'=>$content,'cover_image'=>$image,'active'=>$active]);
 ahead($post['id']?'Edit blog story':'Add blog story','blogs.php');
 ?>
 <?php if($error): ?><p class="bad"><?=e($error)?></p><?php endif; ?>
 <form method="post" class="fm blog-admin-form"><?=csrf()?><input type="hidden" name="id" value="<?=$post['id']?>">
 <label>Story title<input name="title" maxlength="180" required value="<?=e($post['title'])?>"></label>
 <label>URL slug<input name="slug" maxlength="200" required value="<?=e($post['slug'])?>"><small class="mut">Used in the story link, for example: /blog/<?=e($post['slug']?:'your-story-title')?></small></label>
 <label>Short summary<textarea name="excerpt" rows="3" maxlength="500" required><?=e($post['excerpt'])?></textarea></label>
 <label>Cover image URL or site path<input name="cover_image" maxlength="500" value="<?=e($post['cover_image'])?>" placeholder="assets/blog/story-photo.jpg"><small class="mut">Optional. Use an https image URL or a path beginning with assets/.</small></label>
 <label>Story text<textarea name="content" rows="18" required placeholder="Write the story here. Separate paragraphs with a blank line."><?=e($post['content'])?></textarea><small class="mut">Separate paragraphs with a blank line. Text is displayed safely as plain text.</small></label>
 <label class="chk"><input type="checkbox" name="active" <?=$post['active']?'checked':''?>> Published on the site</label>
 <div class="blog-admin-actions"><button class="btn">Save story</button><a href="blogs.php">Cancel</a><?php if($post['id']): ?><button class="btn ghost" name="delete" value="1" onclick="return confirm('Delete this blog story permanently?')">Delete story</button><?php endif; ?></div>
 </form><?php afoot();exit;
}
ahead('Blog stories','blogs.php');
?>
<?php if(isset($_GET['saved'])): ?><p class="okm">Story saved.</p><?php endif; ?><?php if(isset($_GET['deleted'])): ?><p class="okm">Story deleted.</p><?php endif; ?>
<p class="mut">Published stories appear on the homepage and blog. Draft stories are only visible here.</p><p><a class="btn" href="?edit=0">+ Add blog story</a></p>
<table class="tb"><tr><th>Story</th><th>Updated</th><th>Status</th></tr><?php foreach($db->query('SELECT id,title,slug,active,updated_at FROM blog_posts ORDER BY updated_at DESC,id DESC') as $post): ?><tr><td><a href="?edit=<?=$post['id']?>"><?=e($post['title'])?></a><br><small class="mut">/blog/<?=e($post['slug'])?></small></td><td><?=e(date('j M Y',strtotime($post['updated_at'])))?></td><td><?=badge($post['active']?'completed':'cancelled',$post['active']?'Published':'Draft')?></td></tr><?php endforeach; ?></table>
<?php afoot();
