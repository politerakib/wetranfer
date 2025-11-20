      </div>
    </main>
    <footer class="mt-auto border-top bg-body shadow-sm">
      <div class="container py-5">
        <div class="row gy-4 align-items-center">
          <div class="col-lg-4 d-flex align-items-center gap-3">
            <div class="rounded-3 text-white fw-bold d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent)); box-shadow:0 10px 30px rgba(79,70,229,0.25);">P2P</div>
            <div>
              <div class="fw-semibold">NovaRooms</div>
              <p class="mb-0 text-body-secondary small">Fast, secure, and private peer-to-peer rooms.</p>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="row g-3 text-body-secondary small">
              <div class="col-6 col-md-4">
                <div class="fw-semibold text-body">Quick Links</div>
                <ul class="list-unstyled mt-2 mb-0">
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="index.php">Home</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="rooms.php">Rooms</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="about.php">About</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="terms.php">Terms</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="privacy.php">Privacy</a></li>
                </ul>
              </div>
              <div class="col-6 col-md-4">
                <div class="fw-semibold text-body">Support</div>
                <ul class="list-unstyled mt-2 mb-0">
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="terms.php">FAQ</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="#">Status</a></li>
                  <li><a class="link-body-emphasis link-underline-opacity-0" href="#">Contact</a></li>
                </ul>
              </div>
              <div class="col-12 col-md-4">
                <div class="fw-semibold text-body">Theme</div>
                <div class="d-flex align-items-center gap-3 p-3 rounded-4 border bg-body-secondary border-opacity-50 mt-2">
                  <div class="rounded-circle" style="width:34px;height:34px;background:linear-gradient(135deg,var(--brand-primary),var(--brand-accent));"></div>
                  <div class="small text-body-secondary">Auto adapts to your system. Toggle anytime.</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 text-lg-end text-body-secondary small">
            <div class="fw-semibold text-body">Stay secure</div>
            <div>Encrypted by default, zero storage.</div>
          </div>
        </div>
      </div>
      <div class="border-top py-3 text-center text-body-secondary small">
        © <?= date('Y'); ?> NovaRooms. Crafted for secure peer-to-peer collaboration.
      </div>
    </footer>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const htmlEl = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const iconSun = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    const applyTheme = (mode) => {
      htmlEl.setAttribute('data-bs-theme', mode);
      if (mode === 'dark') {
        iconSun?.classList.remove('d-none');
        iconMoon?.classList.add('d-none');
      } else {
        iconSun?.classList.add('d-none');
        iconMoon?.classList.remove('d-none');
      }
      localStorage.setItem('theme', mode);
    };

    applyTheme(storedTheme ? storedTheme : prefersDark ? 'dark' : 'light');

    themeToggle?.addEventListener('click', () => {
      const nextTheme = htmlEl.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(nextTheme);
    });

    document.querySelectorAll('[data-password-toggle]')?.forEach(toggle => {
      toggle.addEventListener('click', () => {
        const target = document.getElementById(toggle.dataset.passwordToggle);
        if (!target) return;
        target.type = target.type === 'password' ? 'text' : 'password';
        toggle.querySelector('[data-eye-open]')?.classList.toggle('d-none');
        toggle.querySelector('[data-eye-closed]')?.classList.toggle('d-none');
      });
    });
  </script>
</body>
</html>
