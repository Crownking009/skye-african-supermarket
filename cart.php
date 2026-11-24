<?php require 'inc/layout.php';$GLOBALS['seo']=['noindex'=>true];head('Your basket | Skye African Supermarket');?>
<main class="wrap1"><h1 class="ph1">Your basket</h1><div id="empty" hidden><p>Your basket is empty.</p><a class="b1x" href="shop">Start shopping</a></div>
<div class="cogrid" id="cogrid"><section class="pn2" id="cl"></section><aside class="sum stk"><h2>Summary</h2><div id="bar"></div><div class="tr big"><span>Subtotal</span><b id="ssub"></b></div><small class="fine">Delivery and promo codes are added at checkout.</small><a class="co" href="checkout">Go to checkout</a><a class="kp2" href="shop">Keep shopping</a></aside></div>
<?php row('You may also like',pick("p.stock>0",[],4)); ?></main>
<?php foot(['assets/cart.js']);
