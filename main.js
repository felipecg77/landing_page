// Main JavaScript - Comida Para Todos Landing Page

document.addEventListener('DOMContentLoaded', () => {
  // 1. Role Selector Tabs
  const roleTabs = document.querySelectorAll('.role-tab');
  const roleContents = document.querySelectorAll('.role-content');

  roleTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const targetTab = tab.getAttribute('data-tab');

      // Update active tab button
      roleTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      // Update visible content section
      roleContents.forEach(content => {
        if (content.id === targetTab) {
          content.classList.add('active');
        } else {
          content.classList.remove('active');
        }
      });
    });
  });

  // 2. FAQ Accordion
  const faqQuestions = document.querySelectorAll('.faq-question');

  faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
      const item = question.parentElement;
      const answer = item.querySelector('.faq-answer');
      const isActive = item.classList.contains('active');

      // Close all active items
      document.querySelectorAll('.faq-item').forEach(i => {
        i.classList.remove('active');
        const a = i.querySelector('.faq-answer');
        if (a) a.style.maxHeight = null;
      });

      // Open clicked item if wasn't active
      if (!isActive) {
        item.classList.add('active');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });

  // 3. Launch Countdown (Target: 01 de Noviembre de 2026 00:00:00)
  const targetDate = new Date('2026-11-01T00:00:00').getTime();

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
      document.querySelectorAll('#cd-days, #m-days').forEach(e => e.innerText = '00');
      document.querySelectorAll('#cd-hours, #m-hours').forEach(e => e.innerText = '00');
      document.querySelectorAll('#cd-minutes, #m-mins').forEach(e => e.innerText = '00');
      document.querySelectorAll('#cd-seconds, #m-secs').forEach(e => e.innerText = '00');
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    const formatNum = (num) => num < 10 ? `0${num}` : num;

    // Update main section countdown
    const elDays = document.getElementById('cd-days');
    const elHours = document.getElementById('cd-hours');
    const elMins = document.getElementById('cd-minutes');
    const elSecs = document.getElementById('cd-seconds');

    if (elDays) elDays.innerText = formatNum(days);
    if (elHours) elHours.innerText = formatNum(hours);
    if (elMins) elMins.innerText = formatNum(minutes);
    if (elSecs) elSecs.innerText = formatNum(seconds);

    // Update modal countdown
    const mDays = document.getElementById('m-days');
    const mHours = document.getElementById('m-hours');
    const mMins = document.getElementById('m-mins');
    const mSecs = document.getElementById('m-secs');

    if (mDays) mDays.innerText = formatNum(days);
    if (mHours) mHours.innerText = formatNum(hours);
    if (mMins) mMins.innerText = formatNum(minutes);
    if (mSecs) mSecs.innerText = formatNum(seconds);
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);

  // 4. Pop-Up Modal Auto-Trigger & Close Handlers
  const modal = document.getElementById('launch-modal');
  const modalCloseBtn = document.getElementById('modal-close');
  const preRegForm = document.getElementById('pre-register-form');

  if (modal) {
    // Abrir modal automáticamente después de 1.2s solo si el usuario no ingresó directo a una sección como #descargar
    const hasSpecificHash = window.location.hash && window.location.hash !== '#' && window.location.hash !== '#inicio';
    if (!hasSpecificHash) {
      setTimeout(() => {
        modal.classList.add('active');
      }, 1200);
    }

    // Close button
    if (modalCloseBtn) {
      modalCloseBtn.addEventListener('click', () => {
        modal.classList.remove('active');
      });
    }

    // Close when clicking outside overlay
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        modal.classList.remove('active');
      }
    });

    // Form submission
    if (preRegForm) {
      preRegForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const phoneInput = document.getElementById('modal-phone');
        const phone = phoneInput ? phoneInput.value.trim() : '';

        if (!phone || phone.length < 10) {
          alert('Por favor ingresa un número de teléfono válido de 10 dígitos.');
          return;
        }

        // 1. Save in local memory (localStorage)
        try {
          const stored = JSON.parse(localStorage.getItem('prerregistros_comida_para_todos') || '[]');
          stored.push({ phone, date: new Date().toISOString() });
          localStorage.setItem('prerregistros_comida_para_todos', JSON.stringify(stored));
        } catch (err) {}

        // 2. Save directly on Hostinger server (registrar.php)
        try {
          await fetch('registrar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ telefono: phone })
          });
        } catch (err) {
          console.log('Error enviando a servidor:', err);
        }

        // 3. Show the exact confirmation message loved by user
        alert(`¡Gracias por tu interés! 🎉\n\nTu número (${phone}) ha sido registrado con éxito. Te avisaremos el 01/11/2026 para que disfrutes del Lanzamiento Oficial.`);
        
        modal.classList.remove('active');
        if (phoneInput) phoneInput.value = '';
      });
    }
  }

  // 4. Navbar Scroll Effect & Active Link Highlight
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.style.background = 'rgba(15, 23, 42, 0.95)';
      navbar.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
    } else {
      navbar.style.background = 'rgba(15, 23, 42, 0.8)';
      navbar.style.boxShadow = 'none';
    }
  });

  // 5. Mobile Nav Toggle
  const mobileToggle = document.getElementById('mobile-toggle');
  const navLinks = document.getElementById('nav-links');

  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        if (navLinks.classList.contains('active')) {
          icon.className = 'fa-solid fa-xmark';
        } else {
          icon.className = 'fa-solid fa-bars';
        }
      }
    });

    // Close menu when clicking a link
    navLinks.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        const icon = mobileToggle.querySelector('i');
        if (icon) icon.className = 'fa-solid fa-bars';
      });
    });
  }
});
