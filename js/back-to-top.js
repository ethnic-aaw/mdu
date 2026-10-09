// ponytail: IntersectionObserver ceiling; scroll listener enough for one-page
const btn=document.getElementById('backToTop');
addEventListener('scroll',()=>{
  btn.classList.toggle('show',scrollY>300);
},{passive:true});
btn?.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
