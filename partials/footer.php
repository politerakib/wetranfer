      </div>
    </main>
    <footer class="border-t border-slate-100 bg-white/90 transition-colors duration-300 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
      <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
            <span class="text-lg font-bold">P2P</span>
          </div>
          <div>
            <p class="text-base font-semibold text-slate-900 dark:text-white">NovaRooms</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Fast, secure, and private peer-to-peer rooms.</p>
          </div>
        </div>
        <div class="grid w-full grid-cols-2 gap-6 text-sm text-slate-600 dark:text-slate-300 sm:grid-cols-3 lg:w-auto">
          <div>
            <p class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Quick Links</p>
            <ul class="space-y-2">
              <li><a href="index.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Home</a></li>
              <li><a href="rooms.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Rooms</a></li>
              <li><a href="about.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">About</a></li>
              <li><a href="terms.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Terms</a></li>
              <li><a href="privacy.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">Privacy</a></li>
            </ul>
          </div>
          <div>
            <p class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Support</p>
            <ul class="space-y-2">
              <li><a href="terms.php" class="hover:text-indigo-600 dark:hover:text-indigo-400">FAQ</a></li>
              <li><a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Status</a></li>
              <li><a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Contact</a></li>
            </ul>
          </div>
          <div class="col-span-2 sm:col-span-1">
            <p class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Theme</p>
            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white/70 px-3 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
              <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600"></div>
              <p class="text-sm text-slate-600 dark:text-slate-300">Auto adapts to your system. Toggle anytime.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="border-t border-slate-100 bg-white/80 py-4 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-400">
        © <?= date('Y'); ?> NovaRooms. Crafted for secure peer-to-peer collaboration.
      </div>
    </footer>
  </div>
  <script>
    const root = document.documentElement;
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const themeToggle = document.getElementById('theme-toggle');
    const iconSun = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');

    const setTheme = (mode) => {
      if (mode === 'dark') {
        root.classList.add('dark');
        iconMoon.classList.add('hidden');
        iconSun.classList.remove('hidden');
      } else {
        root.classList.remove('dark');
        iconMoon.classList.remove('hidden');
        iconSun.classList.add('hidden');
      }
      localStorage.setItem('theme', mode);
    };

    setTheme(storedTheme ? storedTheme : prefersDark ? 'dark' : 'light');

    themeToggle?.addEventListener('click', () => {
      const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';
      setTheme(nextTheme);
    });

    const avatarButton = document.getElementById('avatar-button');
    const avatarMenu = document.getElementById('avatar-menu');
    avatarButton?.addEventListener('click', () => {
      avatarMenu?.classList.toggle('hidden');
    });
    window.addEventListener('click', (event) => {
      if (!avatarButton?.contains(event.target)) {
        avatarMenu?.classList.add('hidden');
      }
    });

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenuToggle?.addEventListener('click', () => mobileMenu?.classList.toggle('hidden'));

    document.querySelectorAll('[data-modal-toggle]')?.forEach(trigger => {
      trigger.addEventListener('click', () => {
        const target = document.getElementById(trigger.dataset.modalToggle);
        target?.classList.remove('hidden');
      });
    });
    document.querySelectorAll('[data-modal-close]')?.forEach(btn => {
      btn.addEventListener('click', () => btn.closest('.modal')?.classList.add('hidden'));
    });

    document.querySelectorAll('[data-accordion]')?.forEach(item => {
      const header = item.querySelector('[data-accordion-header]');
      const content = item.querySelector('[data-accordion-content]');
      header?.addEventListener('click', () => {
        content?.classList.toggle('hidden');
        item.querySelector('svg')?.classList.toggle('rotate-180');
      });
    });

    document.querySelectorAll('[data-password-toggle]')?.forEach(toggle => {
      toggle.addEventListener('click', () => {
        const target = document.getElementById(toggle.dataset.passwordToggle);
        if (!target) return;
        target.type = target.type === 'password' ? 'text' : 'password';
        toggle.querySelector('[data-eye-open]')?.classList.toggle('hidden');
        toggle.querySelector('[data-eye-closed]')?.classList.toggle('hidden');
      });
    });
  </script>
</body>
</html>
