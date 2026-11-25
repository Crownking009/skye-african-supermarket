<?php
require 'inc/layout.php';
$slug=trim((string)($_GET['slug']??''));
if($slug!==''){
 $st=db()->prepare('SELECT * FROM blog_posts WHERE slug=? AND active=1 LIMIT 1');$st->execute([$slug]);$post=$st->fetch();
 if(!$post){http_response_code(404);$GLOBALS['seo']=['noindex'=>true];head('Article not found | Skye African Supermarket');echo '<main class="wrap1"><h1 class="ph1">Article not found</h1><a class="b1x" href="blog">Back to the blog</a></main>';foot();exit;}
 $GLOBALS['seo']=['canonical'=>'blog/'.$post['slug'],'type'=>'article'];
 head($post['title'].' | Skye African Supermarket',$post['excerpt'],crumbs([['Home',''],['Blog','blog'],[$post['title'],'blog/'.$post['slug']]]));
 $paragraphs=preg_split('/\R\s*\R/u',trim($post['content']));
 ?>
 <main class="wrap1 blog-article"><nav class="crumb"><a href="./">Home</a> / <a href="blog">Blog</a> / <?=e($post['title'])?></nav><article>
 <?php if($post['cover_image']): ?><img class="blog-cover" src="<?=e($post['cover_image'])?>" alt="" fetchpriority="high"><?php endif; ?>
 <div class="blog-copy"><p class="blog-date"><?=e(date('j F Y',strtotime($post['created_at'])))?></p><h1 class="ph1"><?=e($post['title'])?></h1><p class="blog-intro"><?=e($post['excerpt'])?></p>
 <?php foreach($paragraphs as $paragraph): if(trim($paragraph)!==''): ?><p><?=nl2br(e(trim($paragraph)))?></p><?php endif; endforeach; ?>
 <a class="b1x" href="shop">Shop ingredients</a></div></article></main>
 <?php
}else{
 $GLOBALS['seo']=['canonical'=>'blog'];
 head('Food and shopping tips | Skye African Supermarket','Guides to African ingredients, pantry essentials and cooking from Skye African Supermarket.');
 $posts=db()->query('SELECT id,title,slug,excerpt,cover_image,created_at FROM blog_posts WHERE active=1 ORDER BY created_at DESC,id DESC')->fetchAll();
 ?>
 <main class="blog-page"><header class="blog-mast"><div class="blog-mast-inner"><p class="blog-kicker">THE SKYE JOURNAL</p><h1>Good food starts with a story.</h1><p>Ingredient guides, kitchen notes and inspiration for the food you love.</p></div></header>
 <section class="blog-list wrap1" aria-label="Latest stories"><div class="blog-list-head"><div><p class="blog-kicker">FROM OUR KITCHEN</p><h2>Latest stories</h2></div><span><?=count($posts)?> <?=count($posts)===1?'story':'stories'?></span></div>
 <?php if($posts): ?><div class="blog-grid"><?php foreach($posts as $i=>$item): ?><article class="blog-card <?=$i===0?'blog-card-featured':''?>">
 <a class="blog-card-image" href="blog/<?=e($item['slug'])?>" aria-label="Read <?=e($item['title'])?>"><?php if($item['cover_image']): ?><img src="<?=e($item['cover_image'])?>" alt="" loading="lazy"><?php else: ?><span class="blog-image-type">SKYE<br>JOURNAL</span><?php endif; ?></a>
 <div class="blog-card-copy"><p class="blog-date"><?=e(date('j F Y',strtotime($item['created_at'])))?></p><h3><a href="blog/<?=e($item['slug'])?>"><?=e($item['title'])?></a></h3><p><?=e($item['excerpt'])?></p><a class="blog-readmore" href="blog/<?=e($item['slug'])?>">Read story <span aria-hidden="true">→</span></a></div></article><?php endforeach; ?></div>
 <?php else: ?><p class="blog-empty">New stories are on the way. Check back soon.</p><?php endif; ?></section></main>
 <?php
}
foot();
