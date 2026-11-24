(function(){const $=s=>document.querySelector(s),reduce=matchMedia('(prefers-reduced-motion:reduce)').matches;
const hero=$('.hero2');if(hero){const sl=[...hero.querySelectorAll('.hs')],dots=hero.querySelector('.hdots'),bar=hero.querySelector('.hprog i'),ms=+hero.dataset.auto||4500;let i=0,tm=0,hold=0;
 sl.forEach((s,k)=>{const b=document.createElement('button');b.type='button';b.setAttribute('aria-label','Slide '+(k+1));b.onclick=()=>go(k);dots.appendChild(b)});
 function go(n){i=(n+sl.length)%sl.length;sl.forEach((s,k)=>s.classList.toggle('on',k===i));[...dots.children].forEach((b,k)=>b.classList.toggle('on',k===i));bar.style.animation='none';void bar.offsetWidth;clearTimeout(tm);if(!reduce&&sl.length>1){bar.style.animation='prog '+ms+'ms linear forwards';tm=setTimeout(()=>hold?go(i):go(i+1),ms)}}
 hero.querySelector('.ha.l').onclick=()=>go(i-1);hero.querySelector('.ha.r').onclick=()=>go(i+1);
 hero.addEventListener('pointerenter',()=>hold=1);hero.addEventListener('pointerleave',()=>hold=0);
 let x0=null;hero.addEventListener('touchstart',e=>x0=e.touches[0].clientX,{passive:true});hero.addEventListener('touchend',e=>{if(x0===null)return;const dx=e.changedTouches[0].clientX-x0;if(Math.abs(dx)>40)go(dx<0?i+1:i-1);x0=null});
 if(sl.length<2){hero.querySelectorAll('.ha,.hdots,.hprog').forEach(e=>e.hidden=true)}go(0)}
const fmt=(n,d)=>d?n.toFixed(1):Math.round(n).toLocaleString('en-GB');
const run=el=>{const to=+el.dataset.count,d=!!el.dataset.dec;if(reduce){el.textContent=fmt(to,d);return}const t0=performance.now();const f=t=>{const p=Math.min((t-t0)/1400,1),e=1-Math.pow(1-p,3);el.textContent=fmt(to*e,d);if(p<1)requestAnimationFrame(f)};requestAnimationFrame(f)};
const cs=document.querySelectorAll('[data-count]');if('IntersectionObserver' in window){const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){run(x.target);io.unobserve(x.target)}}),{threshold:.4});cs.forEach(c=>io.observe(c))}else cs.forEach(run);
document.querySelectorAll('[data-end]').forEach(el=>{const end=new Date(el.dataset.end).getTime();const tick=()=>{const d=end-Date.now();if(d<=0){el.textContent='Ended';return}const h=Math.floor(d/36e5),m=Math.floor(d%36e5/6e4),s=Math.floor(d%6e4/1e3),p=n=>String(n).padStart(2,'0');el.textContent='Ends in '+(h>=48?Math.floor(h/24)+' days '+(h%24)+'h':p(h)+':'+p(m)+':'+p(s));setTimeout(tick,1000)};tick()});
try{const rv=JSON.parse(localStorage.getItem('skye_rv')||'[]');if(rv.length){fetch('api/cards.php?skus='+encodeURIComponent(rv.slice(0,10).join(','))).then(r=>r.text()).then(h=>{if(h.trim()){$('#rvt').innerHTML=h;$('#rvw').hidden=false;window.skyeSlides&&window.skyeSlides($('#rvw .sl'))}}).catch(()=>{})}}catch(e){}
})();
