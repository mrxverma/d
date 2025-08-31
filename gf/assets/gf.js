const chatEl = document.getElementById('chat');
const form = document.getElementById('composer');
const msgInput = document.getElementById('message');
const sendBtn = document.getElementById('sendBtn');
let sessionId = localStorage.getItem('session_id');
if(!sessionId){
  sessionId = Date.now().toString(36)+Math.random().toString(36).slice(2);
  localStorage.setItem('session_id', sessionId);
}
let currentAssistant;
function addMessage(m){
  const div = document.createElement('div');
  div.className = 'msg msg-'+m.role;
  const bubble = document.createElement('div');
  bubble.className = 'bubble';
  bubble.textContent = m.content;
  div.appendChild(bubble);
  chatEl.appendChild(div);
  chatEl.scrollTop = chatEl.scrollHeight;
}
async function loadHistory(){
  const res = await fetch(`api/messages.php?session_id=${sessionId}`);
  if(res.ok){
    const data = await res.json();
    data.items.forEach(addMessage);
  }
}
loadHistory();
form.addEventListener('submit', async e => {
  e.preventDefault();
  const text = msgInput.value.trim();
  if(!text) return;
  addMessage({role:'user', content:text});
  msgInput.value='';
  sendBtn.disabled=true;
  currentAssistant = {role:'assistant', content:''};
  addMessage(currentAssistant);
  const res = await fetch('api/chat.php',{
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({session_id:sessionId, message:text})
  });
  const reader = res.body.getReader();
  const decoder = new TextDecoder();
  let buf='';
  while(true){
    const {done, value} = await reader.read();
    if(done) break;
    buf += decoder.decode(value, {stream:true});
    const parts = buf.split('\n\n');
    buf = parts.pop();
    for(const part of parts){
      if(part.startsWith('data:')){
        const data = part.slice(5).trim();
        if(data === '[DONE]'){
          sendBtn.disabled=false;
        } else {
          currentAssistant.content += data;
          currentAssistant.div = currentAssistant.div || chatEl.lastChild;
          currentAssistant.div.querySelector('.bubble').textContent = currentAssistant.content;
          chatEl.scrollTop = chatEl.scrollHeight;
        }
      }
    }
  }
  sendBtn.disabled=false;
});
