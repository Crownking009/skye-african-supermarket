(function(){
const $=s=>document.querySelector(s),M=n=>'£'+Number(n).toFixed(2),esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const get=()=>{try{return JSON.parse(localStorage.getItem('skye_cart')||'{}')}catch(e){return{}}};
const S=window.skye={get,M,esc,last:[]};
S.list=()=>Object.entries(get()).map(([k,v])=>({key:k,sku:v.sku||k.split('|')[0],variant:v.variant||'',qty:v.qty}));
S.price=async x=>(await fetch('api/price.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.assign({cart:S.list()},x||{}))})).json();
S.set=c=>{localStorage.setItem('skye_cart',JSON.stringify(c));count();document.dispatchEvent(new Event('skye:cart'))};
const count=()=>{const n=Object.keys(get()).length;document.querySelectorAll('.cc').forEach(e=>e.textContent=n)};count();
const qt=l=>l.unit?`${+l.qty} ${esc(l.unit)}`:`${+l.qty}`;
S.linesHTML=ls=>ls.map(l=>{const k=esc(l.key);return `<div class="ln"><div class="th">${l.img?`<img src="${esc(l.img)}" alt="">`:esc(l.ini||'')}</div><div class="lt"><b>${esc(l.name)}</b>${l.variant?`<small>${esc(l.variant)}</small>`:''}<small>${M(l.unit_price)}${l.unit?' / '+esc(l.unit):''}</small>${l.stock<l.qty?`<small class="bad">${l.stock>0?'Only '+l.stock+' left':'Out of stock'}</small>`:''}</div><div class="lq"><button type="button" data-act="dec" data-key="${k}" aria-label="Less">−</button><span>${qt(l)}</span><button type="button" data-act="inc" data-key="${k}" aria-label="More">+</button></div><div class="lp">${M(l.total)}<button type="button" class="rm" data-act="rm" data-key="${k}">Remove</button></div></div>`}).join('');
S.bar=(s,f)=>s>=f?`<div class="fb"><div><i style="width:100%"></i></div><small>You have free delivery in our delivery zones</small></div>`:`<div class="fb"><div><i style="width:${Math.round(s/f*100)}%"></i></div><small>Add ${M(f-s)} more for free delivery</small></div>`;
document.addEventListener('click',e=>{const b=e.target.closest('[data-act]');if(!b)return;const c=get(),k=b.dataset.key;if(!c[k])return;
 if(b.dataset.act==='rm')delete c[k];else{const l=S.last.find(x=>x.key===k)||{step:1,min:1},q=(+c[k].qty)+(b.dataset.act==='inc'?l.step:-l.step);if(q<l.min-1e-9)delete c[k];else c[k].qty=Math.round(q*100)/100}
 S.set(c)});
document.body.insertAdjacentHTML('beforeend','<div id="ov" hidden></div><aside id="drawer" aria-label="Basket" hidden><div class="dh"><h2>Your basket</h2><button type="button" id="dx" aria-label="Close">×</button></div><div id="dl"></div><div id="df"></div></aside>');
const dr=$('#drawer'),ov=$('#ov');
const open=()=>{dr.hidden=ov.hidden=false;render()},close=()=>{dr.hidden=ov.hidden=true};
async function render(){if(dr.hidden)return;const r=await S.price();S.last=r.lines;
 $('#dl').innerHTML=r.lines.length?S.linesHTML(r.lines):'<p class="em">Your basket is empty.<br><a href="shop">Start shopping</a></p>';
 $('#df').innerHTML=r.lines.length?`${S.bar(r.subtotal,r.free_over)}<div class="tr"><span>Subtotal</span><b>${M(r.subtotal)}</b></div><a class="co" href="checkout">Go to checkout</a><a class="kp2" href="cart">View full basket</a><button type="button" class="kp" id="kp">Keep shopping</button>`:''}
document.addEventListener('skye:cart',render);
dr.addEventListener('click',e=>{if(e.target.id==='kp')close()});$('#dx').onclick=close;ov.onclick=close;
document.querySelectorAll('.cartpill,.bn-basket').forEach(b=>b.addEventListener('click',e=>{e.preventDefault();open()}));const bs=$('.bn-search');if(bs)bs.onclick=()=>{scrollTo({top:0,behavior:'smooth'});setTimeout(()=>q.focus(),250)};
document.addEventListener('click',e=>{const b=e.target.closest('.add');if(!b)return;
 const card=b.closest('.card'),sel=card.querySelector('.var'),c=get(),v=sel?sel.value:'',key=b.dataset.sku+(sel?'|'+v:'');
 const qi=card.querySelector('.qty'),add=qi?(parseFloat(qi.value)||1):(parseFloat(b.dataset.step)||1);
 c[key]=c[key]||{sku:b.dataset.sku,name:b.dataset.name,variant:v,qty:0};c[key].qty=Math.round((c[key].qty+add)*100)/100;
 S.set(c);if(!$('#cof'))open();else{b.textContent='Added';setTimeout(()=>b.textContent='Add to basket',1200)}});
document.addEventListener('change',e=>{if(e.target.classList.contains('var')){const o=e.target.selectedOptions[0];e.target.closest('.card').querySelector('.p').innerHTML=o.dataset.p}});
const q=$('#q'),sug=$('#sug');let t;
q.addEventListener('input',()=>{clearTimeout(t);const v=q.value.trim();if(v.length<2){sug.hidden=true;return}
 t=setTimeout(async()=>{try{const r=await(await fetch('api/suggest.php?q='+encodeURIComponent(v))).json();
 const h=(r.cats||[]).map(c=>`<a href="${c.u}"><b>${esc(c.n)}</b> <small>aisle</small></a>`).concat((r.products||[]).map(p=>`<a href="${p.u}">${esc(p.n)} <small>${esc(p.c)} · ${p.p}</small></a>`)).join('');
 sug.innerHTML=h;sug.hidden=!h}catch(e){}},180)});
document.addEventListener('click',e=>{if(!e.target.closest('.sb'))sug.hidden=true});
document.addEventListener('click',e=>{const b=e.target.closest('[data-copy]');if(b){navigator.clipboard&&navigator.clipboard.writeText(b.dataset.copy);const t=b.textContent;b.textContent='Copied';setTimeout(()=>b.textContent=t,1200)}});
const up=document.createElement('button');up.id='up';up.setAttribute('aria-label','Back to top');up.hidden=true;up.innerHTML='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 15l7-7 7 7"/></svg>';document.body.appendChild(up);addEventListener('scroll',()=>{up.hidden=scrollY<600},{passive:true});up.onclick=()=>scrollTo({top:0,behavior:'smooth'});
addEventListener('scroll',()=>document.body.classList.toggle('scrolled',scrollY>60),{passive:true});
const rvs=document.querySelectorAll('.rv');if('IntersectionObserver' in window){const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){x.target.classList.add('in');io.unobserve(x.target)}}),{threshold:.1});rvs.forEach(x=>io.observe(x))}else rvs.forEach(x=>x.classList.add('in'));
if(/joined=1/.test(location.search))alert('Thanks, you are on our list!');
})();
