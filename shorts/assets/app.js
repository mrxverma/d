let page=1,pageSize=10,loading=false,hasMore=true;
const feed=document.getElementById('feed');
const sentinel=document.getElementById('sentinel');
const template=document.getElementById('slide-template');

async function fetchShorts(){
  if(loading||!hasMore) return;
  loading=true;
  const res=await fetch(`${API.shorts}?page=${page}&page_size=${pageSize}`);
  const data=await res.json();
  data.items.forEach(item=>{
    const slide=createSlide(item);
    feed.appendChild(slide);
    observeSlide(slide);
  });
  hasMore=data.has_more;
  page++;
  loading=false;
}

function parseYtId(link){
  try{
    const url=new URL(link,location.href);
    if(url.hostname.includes('youtu.be')) return url.pathname.split('/')[1];
    if(url.pathname.includes('/shorts/')) return url.pathname.split('/shorts/')[1];
    if(url.searchParams.get('v')) return url.searchParams.get('v');
    return link;
  }catch(e){return link;}
}

function createSlide(item){
  const frag=template.content.cloneNode(true);
  const slide=frag.querySelector('.slide');
  slide.dataset.id=item.id;
  const wrap=slide.querySelector('.video-wrap');
  wrap.dataset.ytid=parseYtId(item.link);
  slide.querySelector('.like-count').textContent=item.likes_count;
  slide.querySelector('.like-btn').addEventListener('click',()=>like(item.id,slide));
  const commentBtn=slide.querySelector('.comment-btn');
  const commentsEl=slide.querySelector('.comments');
  commentBtn.addEventListener('click',()=>{
    const open=commentsEl.dataset.open==='true';
    commentsEl.dataset.open=!open;
    commentsEl.setAttribute('aria-expanded',(!open).toString());
    if(!open) loadComments(item.id,commentsEl);
  });
  const form=slide.querySelector('.comment-form');
  form.addEventListener('submit',e=>{
    e.preventDefault();
    const username=form.username.value.trim();
    const comment=form.comment.value.trim();
    if(!username||!comment) return;
    postComment(item.id,username,comment,commentsEl);
    form.reset();
  });
  return slide;
}

async function like(id,slide){
  if(localStorage.getItem('liked:'+id)) return;
  localStorage.setItem('liked:'+id,'1');
  const countEl=slide.querySelector('.like-count');
  countEl.textContent=parseInt(countEl.textContent)+1;
  const res=await fetch(API.like,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`short_id=${id}`});
  const data=await res.json();
  if(data.likes_count!==undefined) countEl.textContent=data.likes_count;
}

async function loadComments(id,container){
  const list=container.querySelector('.comment-list');
  list.innerHTML='';
  const res=await fetch(`${API.comments}?short_id=${id}`);
  const data=await res.json();
  data.items.forEach(c=>{
    const li=document.createElement('li');
    li.textContent=`${c.username}: ${c.comment}`;
    list.appendChild(li);
  });
}

async function postComment(id,username,comment,container){
  const list=container.querySelector('.comment-list');
  const body=`short_id=${id}&username=${encodeURIComponent(username)}&comment=${encodeURIComponent(comment)}`;
  const res=await fetch(API.commentAdd,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body});
  const data=await res.json();
  if(data.id){
    const li=document.createElement('li');
    li.textContent=`${data.username}: ${data.comment}`;
    list.prepend(li);
  }
}

function setActive(slide){
  const wrap=slide.querySelector('.video-wrap');
  const ytid=wrap.dataset.ytid;
  let iframe=wrap.querySelector('iframe');
  if(!iframe){
    iframe=document.createElement('iframe');
    iframe.setAttribute('allow','autoplay; encrypted-media');
    iframe.setAttribute('allowfullscreen','');
    wrap.appendChild(iframe);
  }
  iframe.src=`https://www.youtube.com/embed/${ytid}?autoplay=1&mute=1&playsinline=1&controls=0&rel=0&modestbranding=1&loop=1&playlist=${ytid}`;
}
function clearActive(slide){
  const iframe=slide.querySelector('iframe');
  if(iframe) iframe.src='';
}

const videoObserver=new IntersectionObserver(entries=>{
  entries.forEach(entry=>{
    if(entry.isIntersecting&&entry.intersectionRatio>0.7){
      setActive(entry.target);
    }else{
      clearActive(entry.target);
    }
  });
},{threshold:0.7});

const activeObserver=new IntersectionObserver(entries=>{
  entries.forEach(entry=>{
    if(entry.isIntersecting&&entry.intersectionRatio>0.8){
      document.querySelectorAll('.slide').forEach(s=>s.classList.remove('slide--active'));
      entry.target.classList.add('slide--active');
    }
  });
},{threshold:0.8});

function observeSlide(slide){
  videoObserver.observe(slide);
  activeObserver.observe(slide);
}

const sentinelObserver=new IntersectionObserver(entries=>{
  if(entries[0].isIntersecting) fetchShorts();
});
sentinelObserver.observe(sentinel);

document.getElementById('next-btn').addEventListener('click',nextSlide);
document.getElementById('prev-btn').addEventListener('click',prevSlide);
document.addEventListener('keydown',e=>{
  if(e.key==='ArrowDown') nextSlide();
  if(e.key==='ArrowUp') prevSlide();
});

function nextSlide(){
  const active=document.querySelector('.slide--active');
  if(active&&active.nextElementSibling) active.nextElementSibling.scrollIntoView({behavior:'smooth'});
}
function prevSlide(){
  const active=document.querySelector('.slide--active');
  if(active&&active.previousElementSibling) active.previousElementSibling.scrollIntoView({behavior:'smooth'});
}

fetchShorts();
