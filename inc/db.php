<?php require __DIR__.'/../config.php';
if(!function_exists('mb_strlen')){function mb_strlen($s){return (int)preg_match_all('/./us',(string)$s);}}
if(!function_exists('mb_substr')){function mb_substr($s,$a,$l=null){return implode('',array_slice(preg_split('//u',(string)$s,-1,PREG_SPLIT_NO_EMPTY)?:[],$a,$l));}}
if(!function_exists('mb_strtoupper')){function mb_strtoupper($s){return strtoupper((string)$s);}}
function db(){static $p;if(!$p)$p=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>true]);return $p;}
function rate_allowed($key,$limit,$window=900,$lock=900){$p=db();$now=time();$key=hash('sha256',(string)$key);$p->prepare('INSERT IGNORE INTO request_limits(bucket,hits,window_start,blocked_until) VALUES(?,0,?,0)')->execute([$key,$now]);$p->beginTransaction();try{$s=$p->prepare('SELECT hits,window_start,blocked_until FROM request_limits WHERE bucket=? FOR UPDATE');$s->execute([$key]);$r=$s->fetch();if((int)$r['blocked_until']>$now){$p->commit();return false;}if($now-(int)$r['window_start']>=$window){$r['hits']=0;$r['window_start']=$now;}$hits=(int)$r['hits']+1;$blocked=$hits>$limit?$now+$lock:0;$p->prepare('UPDATE request_limits SET hits=?,window_start=?,blocked_until=? WHERE bucket=?')->execute([$hits,$r['window_start'],$blocked,$key]);$p->commit();return $hits<=$limit;}catch(Throwable $x){if($p->inTransaction())$p->rollBack();throw $x;}}
function rate_clear($key){db()->prepare('DELETE FROM request_limits WHERE bucket=?')->execute([hash('sha256',(string)$key)]);}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function slug($s){return trim(preg_replace('/[^a-z0-9]+/','-',strtolower($s)),'-');}
function safe_link($s,$fallback='shop'){$s=trim((string)$s);if(preg_match('#^https://[^\s<>"\']+$#i',$s)&&filter_var($s,FILTER_VALIDATE_URL))return $s;if(preg_match('#^(?!//)[/A-Za-z0-9._~?&=%\#-]+$#D',$s))return $s;return $fallback;}
function money($v){return '£'.number_format((float)$v,2);}
// Synonym groups: searching any word also matches the others
const SYN=[['garri','gari','eba'],['ugu','pumpkin leaves','fluted'],['okra','okro'],['egusi','melon'],['poundo','pounded yam','iyan'],['plantain','dodo'],['braids','braiding','xpression','kanekalon'],['wig','wigs','lace'],['hair','bundle','bundles','extensions'],['shea','shea butter'],['crayfish','cray fish'],['beans','ewa'],['pepper','ata','scotch bonnet'],['zobo','hibiscus','sorrel'],['malt','malta','maltina'],['yam','isu'],['snail','igbin']];
function terms($q){
 $t=preg_split('/\s+/',strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]/u',' ',$q))),-1,PREG_SPLIT_NO_EMPTY);$out=[];
 foreach($t as $w){$g=[$w];foreach(SYN as $s)if(in_array($w,$s))$g=array_merge($g,$s);$out[]=array_values(array_unique($g));}
 return $out;}
function find($q='',$f=[],$limit=48){
 $w=['p.status="active"'];$s=['0'];$a=[];$i=0;
 foreach(terms($q) as $g){$or=[];foreach($g as $x){$k=':t'.(++$i);$a[$k]='%'.$x.'%';$or[]="p.name LIKE $k OR p.search_keywords LIKE $k";$s[]="(p.name LIKE $k)*3";}$w[]='('.implode(' OR ',$or).')';}
 if(!empty($f['cat'])){$w[]='(s.slug=:c OR t.slug=:c OR EXISTS(SELECT 1 FROM product_category_map pcm JOIN categories pc ON pc.id=pcm.category_id LEFT JOIN categories pt ON pt.id=pc.parent_id WHERE pcm.product_id=p.id AND (pc.slug=:c OR pt.slug=:c)))';$a[':c']=$f['cat'];}
 if(!empty($f['origin'])){$w[]='p.origin=:o';$a[':o']=$f['origin'];}
 if(!empty($f['max'])){$w[]='p.base_price<=:m';$a[':m']=(float)$f['max'];}
 $o=['price_asc'=>'p.base_price ASC','price_desc'=>'p.base_price DESC','name'=>'p.name ASC','new'=>'p.id DESC','pop'=>'(SELECT COUNT(*) FROM order_items i WHERE i.sku=p.sku) DESC,p.name ASC'][$f['sort']??'']??'score DESC,p.name ASC';
 $st=db()->prepare('SELECT SQL_CALC_FOUND_ROWS p.*,s.name sub,s.slug subslug,t.name top,('.implode('+',$s).') score FROM products p JOIN categories s ON s.id=p.category_id LEFT JOIN categories t ON t.id=s.parent_id WHERE '.implode(' AND ',$w)." ORDER BY $o LIMIT ".(int)$limit." OFFSET ".(int)($f['offset']??0));
 $st->execute($a);$r=$st->fetchAll();$GLOBALS['total']=(int)db()->query('SELECT FOUND_ROWS()')->fetchColumn();
 if(!$r&&$q&&empty($GLOBALS['fuzzy'])){$GLOBALS['fuzzy']=1;$q2=implode(' ',array_map(fn($x)=>mb_strlen($x)>4?mb_substr($x,0,4):$x,preg_split('/\s+/',$q)));if($q2!==$q)return find($q2,$f,$limit);}
 return $r;}
function variants($ids){if(!$ids)return[];$o=[];foreach(db()->query('SELECT * FROM product_variants WHERE product_id IN('.implode(',',array_map('intval',$ids)).') ORDER BY product_id,sort') as $v)$o[$v['product_id']][]=$v;return $o;}
function plabel($p){$u=' <small>/ '.e($p['unit_label']).'</small>';if($p['sold_by_type']=='variant')return '<small>from</small> '.money($p['base_price']);$o=(float)($p['old_price']??0);if($o>(float)$p['base_price'])return '<s>'.money($o).'</s> <b class="sale">'.money($p['base_price']).'</b>'.$u;return money($p['base_price']).$u;}
function tile($p){$f=product_image_path($p['sku']);if($f)return '<img src="'.e($f.'?v='.filemtime(__DIR__.'/../'.$f)).'" alt="'.e($p['name']).'" loading="lazy">';
 $c=[['#EFE6F6','#4B1670'],['#E0F2E8','#0E8A4B'],['#FDF0CF','#8A5A00']][crc32($p['top']??'')%3];
 return '<div class="ph" style="background:'.$c[0].';color:'.$c[1].'">'.e(mb_strtoupper(mb_substr($p['name'],0,1))).'</div>';}
function setting($k,$d=null){static $c;if($c===null){try{$c=db()->query('SELECT k,v FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);}catch(Exception $x){$c=[];}}return $c[$k]??$d;}
function free_over(){return (float)setting('free_over',FREE_OVER);}
function wa_number(){return setting('whatsapp',WHATSAPP);}
function bank(){$a=setting('acc_number','');if(trim((string)$a)==='')return null;return ['bank'=>setting('bank_name',''),'name'=>setting('acc_name',''),'acc'=>$a,'sort'=>setting('sort_code',''),'note'=>setting('pay_note','')];}
function ensure_payment_col(){try{db()->query('SELECT payment FROM orders LIMIT 0');}catch(Exception $e){try{db()->exec("ALTER TABLE orders ADD COLUMN payment VARCHAR(12) DEFAULT 'cash'");}catch(Exception $x){}}try{db()->query('SELECT old_price FROM products LIMIT 0');}catch(Exception $e){try{db()->exec("ALTER TABLE products ADD COLUMN old_price DECIMAL(8,2) NULL");}catch(Exception $x){}}
 try{db()->query('SELECT featured FROM products LIMIT 0');}catch(Exception $e){try{db()->exec("ALTER TABLE products ADD COLUMN featured TINYINT(1) DEFAULT 0");}catch(Exception $x){}}
 try{db()->query('SELECT 1 FROM collections LIMIT 0');}catch(Exception $e){try{db()->exec("CREATE TABLE collections(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(120) NOT NULL,slug VARCHAR(140) NOT NULL UNIQUE,subtitle VARCHAR(200),color TINYINT DEFAULT 0,sort INT DEFAULT 0,active TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");db()->exec("CREATE TABLE collection_items(id INT AUTO_INCREMENT PRIMARY KEY,collection_id INT NOT NULL,product_id INT NOT NULL,sort INT DEFAULT 0,FOREIGN KEY(collection_id) REFERENCES collections(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");}catch(Exception $x){}}
 try{db()->query('SELECT 1 FROM testimonials LIMIT 0');}catch(Exception $e){try{db()->exec("CREATE TABLE testimonials(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) NOT NULL,rating TINYINT NOT NULL DEFAULT 5,body VARCHAR(600) NOT NULL,when_label VARCHAR(40),sort INT DEFAULT 0,active TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");}catch(Exception $x){}}
 try{db()->query('SELECT 1 FROM hero_slides LIMIT 0');}catch(Exception $e){try{db()->exec("CREATE TABLE hero_slides(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(140) NOT NULL,subtitle VARCHAR(220),btn VARCHAR(40),link VARCHAR(255),color TINYINT DEFAULT 0,sort INT DEFAULT 0,active TINYINT(1) DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");}catch(Exception $x){}}
}
function product_image_path($sku,$n=1){$sku=preg_replace('/[^A-Za-z0-9_-]/','',(string)$sku);$suffix=$n>1?'-'.(int)$n:'';foreach(['jpg','png','webp','jpeg'] as $ext){$f='assets/products/'.$sku.$suffix.'.'.$ext;if(file_exists(__DIR__.'/../'.$f))return $f;}
 if($n===1&&preg_match('/^CAT-(\d+)$/',$sku,$m)){foreach(['png','webp','jpg','jpeg'] as $ext){$f='assets/products/product-'.(int)$m[1].'.'.$ext;if(file_exists(__DIR__.'/../'.$f))return $f;}}
 return null;}
function product_images($sku){$o=[];for($n=1;$n<=3;$n++)if($f=product_image_path($sku,$n))$o[]=$f.'?v='.filemtime(__DIR__.'/../'.$f);return $o;}

function shop_address(){return setting('address',SHOP_ADDRESS);}
function wa_display(){$n=wa_number();return (strlen($n)==12&&substr($n,0,2)=='44')?'+44 '.substr($n,2,4).' '.substr($n,6):'+'.$n;}
function maps_link(){return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(shop_address());}
function hrs(){return ['wd'=>[setting('hw_open','09:00'),setting('hw_close','20:00')],'su'=>[setting('hs_open','11:00'),setting('hs_close','20:00')]];}
function fmt_t($t){[$h,$m]=array_pad(explode(':',$t),2,'00');$h=(int)$h;return (($h%12)?:12).($m!='00'?':'.$m:'').($h>=12?'pm':'am');}
function hours_lines(){$h=hrs();return ['Monday - Saturday: '.fmt_t($h['wd'][0]).' - '.fmt_t($h['wd'][1]),'Sunday: '.fmt_t($h['su'][0]).' - '.fmt_t($h['su'][1])];}
function open_status(){$d=new DateTime('now',new DateTimeZone('Europe/London'));$h=hrs();$w=(int)$d->format('w');$k=$w==0?'su':'wd';$n=$d->format('H:i');
 if($n>=$h[$k][0]&&$n<$h[$k][1])return ['open'=>true,'text'=>'Open now, closes at '.fmt_t($h[$k][1])];
 if($n<$h[$k][0])return ['open'=>false,'text'=>'Closed, opens today at '.fmt_t($h[$k][0])];
 $x=($w+1)%7;$k2=$x==0?'su':'wd';return ['open'=>false,'text'=>'Closed, opens '.($x==0?'on Sunday':'tomorrow').' at '.fmt_t($h[$k2][0])];}

function base_url(){$s=((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https')?'https':'http';$d=rtrim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])),'/');return $s.'://'.$_SERVER['HTTP_HOST'].$d.'/';}
function abs_url($r){return base_url().ltrim($r,'/');}
function site_base(){$script=str_replace('\\','/',$_SERVER['SCRIPT_NAME']??'/');$dir=rtrim(dirname($script),'/');$dir=preg_replace('#/(admin|api)$#','',$dir);return ((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.($_SERVER['SERVER_NAME']??$_SERVER['HTTP_HOST']??'localhost').rtrim($dir,'/').'/';}
function u_product($p){return 'product/'.slug($p['name']).'--'.rawurlencode($p['sku']);}
function u_cat($s){return 'shop/'.$s;}function u_col($s){return 'collection/'.$s;}function u_rec($s){return 'recipe/'.$s;}
function seo_area(){return setting('seo_area','Wolverton, Milton Keynes');}
function jsonld($a){return '<script type="application/ld+json">'.json_encode($a,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';}
function crumbs($items){$l=[];foreach($items as $i=>$c)$l[]=['@type'=>'ListItem','position'=>$i+1,'name'=>$c[0],'item'=>abs_url($c[1])];return jsonld(['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$l]);}
function cat_emoji($s){$m=['fresh-produce'=>'🥬','meat-fish-poultry'=>'🍖','rice-grains-flours'=>'🌾','spices-soup-ingredients'=>'🌶️','oils-sauces-condiments'=>'🫙','snacks-sweets'=>'🍿','drinks'=>'🥤','bakery-dairy'=>'🍞','frozen-foods'=>'🧊','beauty-hair'=>'💇🏾','skin-care-body'=>'🧴','household-kitchen'=>'🍳','health-wellness'=>'🌿'];return $m[$s]??'🛒';}
