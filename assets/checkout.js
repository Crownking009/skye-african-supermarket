(function(){const S=window.skye,$=s=>document.querySelector(s),f=$('#cof'),E=f.elements;
async function refresh(){const r=await S.price({method:E.method.value,postcode:E.postcode.value,promo:E.promo.value});S.last=r.lines;const del=E.method.value==='delivery',d=r.delivery||{};
 $('#empty').hidden=r.lines.length>0;$('#cogrid').hidden=!r.lines.length;$('#sl').innerHTML=S.linesHTML(r.lines);$('#bar').innerHTML=S.bar(r.subtotal,r.free_over);
 $('#ssub').textContent=S.M(r.subtotal);$('#sdel').textContent=del?(d.zone==='To be confirmed'?'To be confirmed':(d.fee?S.M(d.fee):'Free')):'Collection';$('#stot').textContent=S.M(r.total);const pr=r.promo||{};$('#pmsg').textContent=pr.msg||'';$('#pmsg').className=pr.ok?'dm':'bad';$('#rdisc').hidden=!pr.discount;$('#sdisc').textContent=pr.discount?'-'+S.M(pr.discount):'';
 $('#dmsg').textContent=del?d.note:'';$('#dmsg').className=d.ok===false?'bad':'';$('#ageb').hidden=!r.age;const bk=$('#bank');if(bk){bk.hidden=E.payment.value!=='transfer';$('#paytot').textContent=S.M(r.total)}$('#cnt').textContent='('+r.lines.length+(r.lines.length==1?' item)':' items)');$('#sumb').classList.toggle('stk',r.lines.length<=6);if(innerWidth<820&&!window.__d){$('.sd').open=false;window.__d=1}$('#dfields').hidden=!del}
document.addEventListener('skye:cart',refresh);f.addEventListener('change',refresh);E.postcode.addEventListener('blur',refresh);$('#pa').onclick=refresh;refresh();
f.addEventListener('submit',async e=>{e.preventDefault();const b=$('#place'),er=$('#err');er.textContent='';b.disabled=true;b.textContent='Placing order...';
 const d=Object.fromEntries(new FormData(f));d.age_ok=E.age_ok.checked;d.cart=S.list();
 try{const r=await fetch('api/order.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(d)}),j=await r.json();
  if(j.ok){localStorage.removeItem('skye_cart');location='thanks.php?ref='+encodeURIComponent(j.ref)}else{er.textContent=j.error||'Something went wrong.';b.disabled=false;b.textContent='Place order'}}
 catch(x){er.textContent='Network problem, please try again.';b.disabled=false;b.textContent='Place order'}});
})();
