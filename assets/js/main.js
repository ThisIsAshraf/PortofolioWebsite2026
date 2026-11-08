(() => {
  "use strict";

  // 1. Mobile Navigation Toggle
  const toggle = document.getElementById("menuToggle");
  const links = document.getElementById("navLinks");
  if (toggle && links) {
    toggle.addEventListener("click", () => {
      const open = links.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(open));
    });

    links.querySelectorAll("a").forEach((anchor) => {
      anchor.addEventListener("click", () => links.classList.remove("is-open"));
    });
  }

  // 2. Theme Persistence (Local Storage)
  const themeToggle = document.getElementById("themeToggle");
  const savedTheme = localStorage.getItem("site-theme");
  if (savedTheme === "light") {
    document.body.classList.add("light-theme");
  }

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const isLight = document.body.classList.toggle("light-theme");
      localStorage.setItem("site-theme", isLight ? "light" : "dark");
    });
  }

  // 3. Skills Category Filter
  const filterButtons = document.querySelectorAll("[data-filter]");
  const skillCards = document.querySelectorAll(".skill-card");

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");
      const filter = button.dataset.filter;

      skillCards.forEach((card) => {
        card.hidden = filter !== "all" && card.dataset.group !== filter;
      });
    });
  });

  // 4. Scroll Reveal Animations
  const revealElements = document.querySelectorAll(".reveal");
  const revealOnScroll = () => {
    const triggerBottom = window.innerHeight * 0.92;
    revealElements.forEach((el) => {
      const boxTop = el.getBoundingClientRect().top;
      if (boxTop < triggerBottom) {
        el.classList.add("is-visible");
      }
    });
  };
  window.addEventListener("scroll", revealOnScroll, { passive: true });
  revealOnScroll();

  // 5. Asynchronous Contact Form Submission (Fetch API)
  const contactForm = document.getElementById("contactForm");
  const alertBox = document.getElementById("formAlertBox");
  const submitBtn = document.getElementById("submitBtn");
  const csrfField = document.getElementById("csrfTokenField");

  if (contactForm && alertBox && submitBtn) {
    contactForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const btnText = submitBtn.querySelector(".btn-text");
      const originalText = btnText ? btnText.textContent : "Send message";
      submitBtn.disabled = true;
      if (btnText) btnText.textContent = "Sending...";

      alertBox.innerHTML = "";

      const formData = new FormData(contactForm);

      // Fix: getAttribute('action') prevents collision with <input name="action">
      const targetUrl =
        contactForm.getAttribute("action") || window.location.href;

      try {
        const response = await fetch(targetUrl, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
          body: formData,
        });

        const data = await response.json();

        // Update CSRF token for subsequent requests
        if (data.csrf_token && csrfField) {
          csrfField.value = data.csrf_token;
        }

        const alertDiv = document.createElement("div");
        alertDiv.className = `flash flash-${data.success ? "success" : "error"}`;
        alertDiv.setAttribute("role", "status");
        alertDiv.textContent = data.message;
        alertBox.appendChild(alertDiv);

        if (data.success) {
          contactForm.reset();
        }
      } catch (err) {
        const errDiv = document.createElement("div");
        errDiv.className = "flash flash-error";
        errDiv.textContent =
          "Network or submission error occurred. Please try again.";
        alertBox.appendChild(errDiv);
      } finally {
        submitBtn.disabled = false;
        if (btnText) btnText.textContent = originalText;
      }
    });
  }
})();
