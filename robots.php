<?php require 'inc/db.php';header('Content-Type: text/plain');
echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nDisallow: /checkout\nDisallow: /cart\nDisallow: /thanks.php\nDisallow: /*?q=\nDisallow: /*&q=\nDisallow: /*sort=\nDisallow: /*origin=\nDisallow: /*max=\n\nSitemap: ".abs_url('sitemap.xml')."\n";
