let page=1,pageSize=10,loading=false,hasMore=true;
let soundOn=localStorage.getItem('soundOn')==='1';
const feed=document.getElementById('feed');
const sentinel=document.getElementById('sentinel');
const template=document.getElementById('slide-template');
const offcanvasEl=document.getElementById('commentsCanvas');
const commentList=offcanvasEl.querySelector('.comment-list');
const commentForm=offcanvasEl.querySelector('.comment-form');
let currentCommentShortId=null;

commentForm.addEventListener('submit',e=>{
  e.preventDefault();
  const username=commentForm.username.value.trim();
  const comment=commentForm.comment.value.trim();
  if(!username||!comment||currentCommentShortId===null) return;
  postComment(currentCommentShortId,username,comment);
  commentForm.reset();
});

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
  const likeBtn=slide.querySelector('.like-btn');
  if(localStorage.getItem('liked:'+item.id)) likeBtn.dataset.liked="true";
  likeBtn.addEventListener('click',()=>like(item.id,slide,likeBtn));
  const volBtn=slide.querySelector('.volume-btn');
  updateVolumeBtn(volBtn);
  volBtn.addEventListener('click',toggleSound);
  const commentBtn=slide.querySelector('.comment-btn');
  commentBtn.addEventListener('click',()=>{
    currentCommentShortId=item.id;
    loadComments(item.id);
  });
  return slide;
}

async function like(id,slide,btn){
  if(localStorage.getItem('liked:'+id)) return;
  localStorage.setItem('liked:'+id,'1');
  btn.dataset.liked="true";
  const countEl=slide.querySelector('.like-count');
  countEl.textContent=parseInt(countEl.textContent)+1;
  const res=await fetch(API.like,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`short_id=${id}`});
  const data=await res.json();
  if(data.likes_count!==undefined) countEl.textContent=data.likes_count;
}

async function loadComments(id){
  commentList.innerHTML='';
  const res=await fetch(`${API.comments}?short_id=${id}`);
  const data=await res.json();
  data.items.forEach(c=>{
    const li=document.createElement('li');
    li.textContent=`${c.username}: ${c.comment}`;
    commentList.appendChild(li);
  });
}

async function postComment(id,username,comment){
  const body=`short_id=${id}&username=${encodeURIComponent(username)}&comment=${encodeURIComponent(comment)}`;
  const res=await fetch(API.commentAdd,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body});
  const data=await res.json();
  if(data.id){
    const li=document.createElement('li');
    li.textContent=`${data.username}: ${data.comment}`;
    commentList.prepend(li);
  }
}

function sendCommand(iframe,cmd){
  iframe.contentWindow.postMessage(JSON.stringify({event:'command',func:cmd,args:[]}),'*');
}
function setActive(slide){
  const wrap=slide.querySelector('.video-wrap');
  const ytid=wrap.dataset.ytid;
  let iframe=wrap.querySelector('iframe');
  if(!iframe){
    iframe=document.createElement('iframe');
    iframe.setAttribute('allow','autoplay; encrypted-media');
    iframe.setAttribute('allowfullscreen','');
    iframe.setAttribute('loading','lazy');
    iframe.src=`https://www.youtube.com/embed/${ytid}?enablejsapi=1&playsinline=1&controls=0&rel=0&modestbranding=1&loop=1&playlist=${ytid}&autoplay=1&mute=${soundOn?0:1}`;
    iframe.addEventListener('load',()=>{
      sendCommand(iframe,'playVideo');
    });
    wrap.appendChild(iframe);
  }else{
    sendCommand(iframe,soundOn?'unMute':'mute');
    sendCommand(iframe,'playVideo');
  }
}
function clearActive(slide){
  const iframe=slide.querySelector('iframe');
  if(iframe) sendCommand(iframe,'pauseVideo');
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

function updateVolumeBtn(btn){
  btn.textContent=soundOn?'🔊':'🔇';
}
function toggleSound(){
  soundOn=!soundOn;
  localStorage.setItem('soundOn',soundOn?'1':'0');
  document.querySelectorAll('.volume-btn').forEach(updateVolumeBtn);
  const active=document.querySelector('.slide--active');
  if(active){
    const iframe=active.querySelector('iframe');
    if(iframe) sendCommand(iframe,soundOn?'unMute':'mute');
  }
}

function nextSlide(){
  const active=document.querySelector('.slide--active');
  if(active&&active.nextElementSibling) active.nextElementSibling.scrollIntoView({behavior:'smooth'});
}
function prevSlide(){
  const active=document.querySelector('.slide--active');
  if(active&&active.previousElementSibling) active.previousElementSibling.scrollIntoView({behavior:'smooth'});
}

fetchShorts();
