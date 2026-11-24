(function(){function init(sl){if(sl.dataset.init)return;const t=sl.querySelector('.slt'),L=sl.querySelector('.sa.l'),R=sl.querySelector('.sa.r'),kids=[...t.children];if(!kids.length)return;sl.dataset.init=1;
const w=()=>kids[0].getBoundingClientRect().width+12;
const go=d=>{const max=t.scrollWidth-t.clientWidth;if(d>0&&t.scrollLeft>=max-4)t.scrollTo({left:0,behavior:'smooth'});else if(d<0&&t.scrollLeft<=4)t.scrollTo({left:max,behavior:'smooth'});else t.scrollBy({left:d*w(),behavior:'smooth'})};
L.onclick=()=>go(-1);R.onclick=()=>go(1);let dots=null;
if(sl.dataset.dots){dots=sl.querySelector('.dots');kids.forEach((k,i)=>{const b=document.createElement('button');b.type='button';b.setAttribute('aria-label','Slide '+(i+1));b.onclick=()=>t.scrollTo({left:i*w(),behavior:'smooth'});dots.appendChild(b)})}
const mark=()=>{if(!dots)return;const i=Math.min(Math.round(t.scrollLeft/w()),kids.length-1);[...dots.children].forEach((b,j)=>b.classList.toggle('on',j===i))};t.addEventListener('scroll',mark,{passive:true});mark();
const ms=+sl.dataset.auto;if(ms&&!matchMedia('(prefers-reduced-motion:reduce)').matches){let h=0;setInterval(()=>{if(!h&&!document.hidden)go(1)},ms);['pointerenter','focusin','touchstart'].forEach(e=>sl.addEventListener(e,()=>h=1));['pointerleave','focusout'].forEach(e=>sl.addEventListener(e,()=>h=0));sl.addEventListener('touchend',()=>setTimeout(()=>h=0,5000))}}
window.skyeSlides=init;document.querySelectorAll('.sl').forEach(init)})();
