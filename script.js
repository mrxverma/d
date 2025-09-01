document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Thanks for your message!');
      form.reset();
    });
  }

  const counters = document.querySelectorAll('.counter');
  counters.forEach((counter) => {
    const target = parseFloat(counter.dataset.target);
    const duration = 2000;
    const start = performance.now();

    const step = (timestamp) => {
      const progress = Math.min((timestamp - start) / duration, 1);
      const value = target * progress;
      counter.textContent = target % 1 === 0 ? Math.round(value) : value.toFixed(1);
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        counter.textContent = target % 1 === 0 ? target : target.toFixed(1);
      }
    };

    requestAnimationFrame(step);
  });
});
