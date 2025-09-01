// Countdown
function startCountdown(dateStr){
  const el=document.getElementById('countdown');
  if(!el) return;
  const target=new Date(dateStr);
  function update(){
    const now=new Date();
    const diff=target-now;
    if(diff<=0){el.textContent="It's your day!";return;}
    const d=Math.floor(diff/86400000);
    const h=Math.floor(diff/3600000)%24;
    const m=Math.floor(diff/60000)%60;
    const s=Math.floor(diff/1000)%60;
    el.textContent=`${d}d ${h}h ${m}m ${s}s`;
  }
  update();
  setInterval(update,1000);
}

// Quiz
const quizQuestions=[
  {q:"Kitne aadmi the?",options:["2","3","4","6"],answer:1},
  {q:"Mogambo...",options:["khush hua","sad hua","gussa hua","pyaar hua"],answer:0},
  {q:"Bade bade deshon mein...",options:["aisi choti choti baatein","pyaar ki baatein","shaadiyaan","naach gaana"],answer:0},
  {q:"Tension lene ka nahi...",options:["sirf dene ka","kabhi nahi","sahi time ka","ghar ja"],answer:0},
  {q:"Picture abhi...",options:["baaki hai","khatam","chalu","ruk"],answer:0},
  {q:"Tumhare paas kya hai?",options:["maa","paisa","dil","naam"],answer:0},
  {q:"Dosti ka ek usool hai...",options:["madam","no sorry no thank you","hum tum ek kamre mein band ho","tension mat le"],answer:1}
];

function initQuiz(){
  const quiz=document.getElementById('quiz');
  if(!quiz) return;
  quizQuestions.forEach((item,i)=>{
    const div=document.createElement('div');
    div.innerHTML=`<p>${i+1}. ${item.q}</p>`;
    item.options.forEach((opt,idx)=>{
      const label=document.createElement('label');
      label.innerHTML=`<input type="radio" name="q${i}" value="${idx}"> ${opt}`;
      div.appendChild(label);
      div.appendChild(document.createElement('br'));
    });
    quiz.appendChild(div);
  });
  document.getElementById('submitQuiz').addEventListener('click',()=>{
    let score=0;
    quizQuestions.forEach((item,i)=>{
      const val=document.querySelector(`input[name=q${i}]:checked`);
      if(val && parseInt(val.value,10)===item.answer) score++;
    });
    const res=document.getElementById('quizResult');
    if(score>=4){
      res.innerHTML=`You scored ${score}!<br>"You passed the test. Proof that my filmy queen is unbeatable 🎬💖."<br><a class="jelly" href="starmap.php">Go to Star Map</a>`;
    } else {
      res.innerHTML=`You scored ${score}. Wrong… but your smile still deserves an award 🏆.`;
    }
  });
}

// Wishes
function initWishes(){
  const box=document.getElementById('wishBox');
  if(!box) return;
  const save=document.getElementById('saveWish');
  const last=document.getElementById('lastSaved');
  box.value=localStorage.getItem('bittuWish')||'';
  const lastDate=localStorage.getItem('bittuWishDate');
  if(lastDate) last.textContent='Last saved: '+lastDate;
  save.addEventListener('click',()=>{
    localStorage.setItem('bittuWish',box.value);
    const now=new Date().toLocaleString();
    localStorage.setItem('bittuWishDate',now);
    last.textContent='Last saved: '+now;
  });
}

// Wheel
function initWheel(){
  const canvas=document.getElementById('wheelCanvas');
  if(!canvas) return;
  const ctx=canvas.getContext('2d');
  const opts=['1 Compliment','Movie Night Pick','1 Memory','Surprise Link','Meme Drop','Starry Note','Virtual Chocolate','Future Dream'];
  const arc=2*Math.PI/opts.length;
  opts.forEach((text,i)=>{
    ctx.beginPath();
    ctx.moveTo(150,150);
    ctx.arc(150,150,150,arc*i,arc*(i+1));
    ctx.fillStyle=`hsl(${i*360/opts.length},80%,70%)`;
    ctx.fill();
    ctx.save();
    ctx.translate(150,150);
    ctx.rotate(arc*(i+0.5));
    ctx.fillStyle='#333';
    ctx.font='14px "Comic Neue"';
    ctx.fillText(text,40,0);
    ctx.restore();
  });
  document.getElementById('spinWheel').addEventListener('click',()=>{
    const index=Math.floor(Math.random()*opts.length);
    const deg=360*5 + index*(360/opts.length);
    canvas.style.transform=`rotate(${deg}deg)`;
    setTimeout(()=>showWheelResult(opts[index]),4000);
  });
}

function showWheelResult(opt){
  const res=document.getElementById('wheelResult');
  const complimentArr=['Your laugh is my favorite sound!','Even your flaws are flawless.','You light up my life.'];
  const movies=['Dilwale Dulhania Le Jayenge','Zindagi Na Milegi Dobara','Yeh Jawaani Hai Deewani'];
  const memories=['That day you fell from a chair 🤭','Our first late night chat','When you sent that cute snap'];
  const dreams=['One day we’ll travel the world together','We will binge-watch our favorite shows','We’ll celebrate every birthday side by side'];
  let msg='';
  switch(opt){
    case '1 Compliment':
      msg=randomFrom(complimentArr);break;
    case 'Movie Night Pick':
      msg='Tonight watch: '+randomFrom(movies);break;
    case '1 Memory':
      msg=randomFrom(memories);break;
    case 'Surprise Link':
      msg='<a href="gallery.php">Open the gallery</a>';break;
    case 'Meme Drop':
      msg='<img src="assets/images/meme.svg" alt="meme" style="max-width:200px;">';break;
    case 'Starry Note':
      msg='<a href="starmap.php">Look at the stars</a>';break;
    case 'Virtual Chocolate':
      msg='<img src="assets/images/chocolate.svg" alt="chocolate" style="max-width:200px;"><p>Sweet treat for sweetest you!</p>';break;
    case 'Future Dream':
      msg=randomFrom(dreams);break;
  }
  res.innerHTML=msg;
}

function randomFrom(arr){return arr[Math.floor(Math.random()*arr.length)];}

// Love letter typing
function typeLoveLetter(){
  const el=document.getElementById('loveLetter');
  if(!el) return;
  const text="Bittu, you’re my goddess. You dared to love me, to claim me, to trust me. No fairy tale, no Bollywood film could ever match what you mean to me. You’re the only chaos I want in my ordered life. Happy Birthday, my dodo 💖.";
  let i=0;
  (function type(){
    if(i<text.length){
      el.innerHTML+=text.charAt(i);
      i++;
      setTimeout(type,50);
    }
  })();
}

document.addEventListener('DOMContentLoaded',()=>{
  initQuiz();
  initWishes();
  initWheel();
  typeLoveLetter();
});
