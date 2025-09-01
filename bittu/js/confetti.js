function confettiBurst() {
  for (let i = 0; i < 100; i++) {
    const conf = document.createElement('div');
    conf.className = 'confetti';
    conf.style.left = Math.random() * 100 + '%';
    conf.style.backgroundColor = `hsl(${Math.random()*360},100%,70%)`;
    document.body.appendChild(conf);
    const fall = conf.animate([
      { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
      { transform: 'translateY(100vh) rotate(720deg)', opacity: 0 }
    ], {
      duration: 3000 + Math.random() * 2000,
      easing: 'linear'
    });
    fall.onfinish = () => conf.remove();
  }
}

window.addEventListener('load', confettiBurst);
