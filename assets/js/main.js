(() => {
  const toggle = document.getElementById('menuToggle');
  const links = document.getElementById('navLinks');
  if (toggle && links) toggle.addEventListener('click', () => {
    const open = links.classList.toggle('is-open'); toggle.setAttribute('aria-expanded', String(open));
  });
  if (links) links.querySelectorAll('a').forEach(a => a.addEventListener('click', () => links.classList.remove('is-open')));
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) themeToggle.addEventListener('click', () => document.body.classList.toggle('light-theme'));
  document.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
    button.classList.add('active'); const filter = button.dataset.filter;
    document.querySelectorAll('.skill-card').forEach(card => { card.hidden = filter !== 'all' && card.dataset.group !== filter; });
  }));
  const reveal = () => document.querySelectorAll('.reveal').forEach(el => {
    const rect = el.getBoundingClientRect(); if (rect.top < window.innerHeight * .94) el.classList.add('is-visible');
  });
  window.addEventListener('scroll', reveal, {passive:true}); reveal();
})();
