(function(){const $=s=>document.querySelector(s),car=$('#car'),fp=$('#fp'),ov=$('#fov'),g=$('#pg');
const step=()=>Math.max(car.clientWidth*.8,160);$('.ca.l').onclick=()=>car.scrollBy({left:-step(),behavior:'smooth'});$('.ca.r').onclick=()=>car.scrollBy({left:step(),behavior:'smooth'});
const open=()=>{fp.classList.add('open');ov.hidden=false},close=()=>{fp.classList.remove('open');ov.hidden=true};$('#fopen').onclick=open;$('#fclose').onclick=close;ov.onclick=close;
const set=v=>{g.classList.toggle('list',v==='list');document.querySelectorAll('.vt').forEach(b=>b.classList.toggle('on',b.dataset.v===v));try{localStorage.setItem('skye_view',v)}catch(e){}};
document.querySelectorAll('.vt').forEach(b=>b.onclick=()=>set(b.dataset.v));try{set(localStorage.getItem('skye_view')||'grid')}catch(e){}})();
