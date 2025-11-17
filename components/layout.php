<?php
function render_layout(string $pageTitle, string $pageId, string $content, string $modals = '', bool $loggedIn = true, array $user = []): void
{
    $defaultUser = [
        'name' => 'Jordan Lee',
        'email' => 'jordan@example.com',
        'avatar' => 'https://ui-avatars.com/api/?name=Jordan+Lee&background=6366F1&color=fff'
    ];
    $user = array_merge($defaultUser, $user);
    ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="system">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?> · P2P Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5'
                        }
                    }
                }
            },
            darkMode: 'class'
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light dark;
        }
        * { transition: background-color 200ms ease, color 200ms ease, border-color 200ms ease; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">
<?php
$headerUser = $user;
$headerLoggedIn = $loggedIn;
include __DIR__ . '/header.php';
?>
<main id="<?php echo htmlspecialchars($pageId); ?>" class="relative z-0 min-h-[calc(100vh-200px)] pt-24 pb-20">
    <div class="max-w-6xl px-4 mx-auto space-y-16">
        <?php echo $content; ?>
    </div>
</main>
<?php echo $modals; ?>
<?php include __DIR__ . '/footer.php'; ?>
<script>
    const storageKey = 'p2p-theme';
    const root = document.documentElement;
    function setThemeLabel(theme) {
        const label = theme === 'system' ? 'Auto' : theme.charAt(0).toUpperCase() + theme.slice(1);
        document.querySelectorAll('[data-theme-label]').forEach(el => {
            el.textContent = label + ' mode';
        });
    }
    function applyTheme(theme) {
        if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        setThemeLabel(theme);
    }
    const savedTheme = localStorage.getItem(storageKey) || 'system';
    applyTheme(savedTheme);
    document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const current = localStorage.getItem(storageKey) || 'system';
            const next = current === 'light' ? 'dark' : current === 'dark' ? 'system' : 'light';
            localStorage.setItem(storageKey, next);
            applyTheme(next);
            const label = next === 'system' ? 'Auto' : next.charAt(0).toUpperCase() + next.slice(1);
            btn.querySelector('[data-theme-label]').textContent = label + ' mode';
        });
    });
    document.querySelectorAll('[data-menu-toggle]').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const target = document.getElementById(toggle.dataset.menuToggle);
            target?.classList.toggle('hidden');
        });
    });
    document.querySelectorAll('[data-modal-open]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const target = document.getElementById(trigger.dataset.modalOpen);
            target?.classList.remove('opacity-0', 'pointer-events-none');
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const target = trigger.closest('[data-modal]');
            target?.classList.add('opacity-0', 'pointer-events-none');
        });
    });
</script>
</body>
</html>
<?php
}
?>
