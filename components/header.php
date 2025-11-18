<?php
$navLinks = [
    ['label' => 'Home', 'href' => 'index.php'],
    ['label' => 'Rooms', 'href' => 'rooms.php'],
    ['label' => 'Profile', 'href' => 'profile.php'],
    ['label' => 'About', 'href' => 'about.php'],
    ['label' => 'FAQ', 'href' => '#faq']
];

$loggedIn = $headerLoggedIn ?? true;
$userProfile = $headerUser ?? [
    'name' => 'Jordan Lee',
    'email' => 'jordan@example.com',
    'avatar' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=120&q=80'
];
?>
<header class="fixed inset-x-0 top-0 z-20 bg-white/80 shadow-sm backdrop-blur dark:bg-slate-900/90">
    <div class="max-w-6xl px-4 mx-auto">
        <div class="flex items-center justify-between h-16">
            <a href="index.php" class="flex items-center space-x-3 text-lg font-semibold">
                <span class="grid w-10 h-10 place-items-center rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg">P2P</span>
                <span class="hidden text-xl tracking-tight sm:block">P2P Rooms</span>
            </a>
            <nav class="items-center hidden gap-8 text-sm font-medium text-slate-700 md:flex dark:text-slate-200">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?php echo $link['href']; ?>" class="hover:text-brand-600 dark:hover:text-brand-400 transition"><?php echo $link['label']; ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center space-x-2">
                <button data-theme-toggle class="flex items-center h-10 px-3 text-sm font-medium border rounded-full border-slate-200/80 bg-white/80 backdrop-blur hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="mr-2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow">☾</span>
                    <span class="hidden sm:inline" data-theme-label>Auto mode</span>
                </button>
                <?php if ($loggedIn): ?>
                    <div class="relative">
                        <button data-menu-toggle="profileMenu" class="relative flex items-center justify-center w-10 h-10 overflow-hidden text-sm font-semibold rounded-full ring-2 ring-brand-200 hover:ring-brand-400 dark:ring-slate-700">
                            <img src="<?php echo $userProfile['avatar']; ?>" alt="Avatar" class="object-cover w-full h-full" />
                        </button>
                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 overflow-hidden rounded-xl border border-slate-100 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-800">
                            <div class="px-4 py-3 text-sm">
                                <p class="font-semibold text-slate-900 dark:text-white"><?php echo $userProfile['name']; ?></p>
                                <p class="text-slate-500 dark:text-slate-300"><?php echo $userProfile['email']; ?></p>
                            </div>
                            <div class="py-2 text-sm text-slate-700 dark:text-slate-200">
                                <a href="#" class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700">Billing</a>
                                <a href="profile.php" class="block px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700">Profile</a>
                                <a href="#" class="block px-4 py-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/40">Sign Out</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hidden items-center gap-2 md:flex">
                        <a href="login.php" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800 dark:text-white">Login</a>
                        <a href="signup.php" class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-lg">Signup</a>
                    </div>
                <?php endif; ?>
                <button data-menu-toggle="mobileMenu" class="inline-flex items-center justify-center w-10 h-10 border rounded-xl md:hidden border-slate-200 bg-white text-slate-700 hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    ☰
                </button>
            </div>
        </div>
        <div id="mobileMenu" class="hidden pb-4 md:hidden">
            <nav class="space-y-1 text-sm font-medium text-slate-700 dark:text-slate-200">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?php echo $link['href']; ?>" class="block px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"><?php echo $link['label']; ?></a>
                <?php endforeach; ?>
                <div class="flex items-center space-x-3 pt-2">
                    <a href="login.php" class="flex-1 px-4 py-2 text-center rounded-lg border border-slate-200 bg-white hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800">Login</a>
                    <a href="signup.php" class="flex-1 px-4 py-2 text-center rounded-lg bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow">Signup</a>
                </div>
            </nav>
        </div>
    </div>
</header>
