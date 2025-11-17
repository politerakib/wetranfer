<?php
require_once __DIR__ . '/components/layout.php';
require_once __DIR__ . '/components/modal.php';

$features = [
    ['title' => 'Ultra-fast relays', 'desc' => 'Optimized P2P routing keeps latency low and streams responsive.', 'icon' => '⚡'],
    ['title' => 'End-to-end privacy', 'desc' => 'Encrypted messages with zero database storage.', 'icon' => '🛡️'],
    ['title' => 'No setup needed', 'desc' => 'Jump into a room instantly without downloads or add-ons.', 'icon' => '🚀'],
    ['title' => 'Cross-device ready', 'desc' => 'Desktop, tablet, and mobile layouts crafted to match.', 'icon' => '📱'],
];

$faqs = [
    ['q' => 'How secure are the rooms?', 'a' => 'Rooms use peer-to-peer encryption and temporary tokens that expire automatically.'],
    ['q' => 'Do I need to create an account?', 'a' => 'You can join public rooms without signing up. Accounts unlock history and custom avatars.'],
    ['q' => 'Can I switch devices mid-call?', 'a' => 'Yes, reconnect from another device and your seat is reserved for 5 minutes.'],
];

ob_start();
?>
<section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-brand-700 px-6 py-12 text-white shadow-xl">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,_rgba(255,255,255,0.08)_1px,_transparent_0)] bg-[size:24px_24px]"></div>
    <div class="relative grid gap-10 lg:grid-cols-2 lg:items-center">
        <div class="space-y-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-100">Premium P2P</p>
            <h1 class="text-3xl font-bold leading-tight sm:text-4xl">Design-forward peer-to-peer rooms for rapid collaboration.</h1>
            <p class="max-w-2xl text-lg text-slate-100/90">Build, host, and join secure rooms with a refined UI kit. Crafted with Tailwind CSS, responsive layouts, and automatic dark mode.</p>
            <div class="flex flex-wrap gap-3">
                <button data-modal-open="joinRoomModal" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-base font-semibold text-slate-900 shadow-lg hover:shadow-xl">
                    Join Room
                </button>
                <a href="rooms.php" class="inline-flex items-center rounded-xl border border-white/30 px-5 py-3 text-base font-semibold text-white hover:bg-white/10">
                    Create P2P Room
                </a>
            </div>
            <div class="flex items-center gap-4 text-sm text-slate-100/80">
                <div class="flex items-center space-x-2"><span class="inline-block h-2 w-2 rounded-full bg-green-400"></span><span>Live presence</span></div>
                <div class="flex items-center space-x-2"><span class="inline-block h-2 w-2 rounded-full bg-sky-300"></span><span>Encrypted relay</span></div>
            </div>
        </div>
        <div class="rounded-2xl bg-white/10 p-6 shadow-2xl backdrop-blur lg:ml-auto">
            <div class="flex items-center justify-between text-sm text-slate-100/90">
                <span>Now in room</span>
                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-medium">8 users</span>
            </div>
            <div class="mt-4 space-y-3">
                <?php foreach (['Carla', 'Max', 'Ivy', 'Ren', 'Sage', 'Noor', 'Theo', 'Mara'] as $member): ?>
                    <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-white/20 text-lg font-semibold"><?php echo strtoupper($member[0]); ?></span>
                            <div>
                                <p class="font-semibold"><?php echo $member; ?></p>
                                <p class="text-xs text-slate-200/80">Active now</p>
                            </div>
                        </div>
                        <span class="text-xs uppercase tracking-wide text-emerald-200">online</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="grid gap-8 md:grid-cols-2" id="faq">
    <div class="space-y-4">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Why choose us</p>
        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Built for speed, privacy, and clarity.</h2>
        <p class="text-slate-600 dark:text-slate-300">A polished UI kit with reusable PHP components: header, footer, modals, cards, and form patterns. Everything you need to launch quickly.</p>
        <div class="grid gap-4 sm:grid-cols-2">
            <?php foreach ($features as $feature): ?>
                <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-800/80">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl"><?php echo $feature['icon']; ?></span>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-200">Realtime</span>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white"><?php echo $feature['title']; ?></h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300"><?php echo $feature['desc']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="space-y-3 rounded-3xl border border-slate-100 bg-white p-6 shadow-md dark:border-slate-800 dark:bg-slate-800/70">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Frequently Asked Questions</h3>
        <div class="space-y-2">
            <?php foreach ($faqs as $index => $faq): ?>
                <details class="group rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-700 dark:bg-slate-900/40">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-slate-900 dark:text-white">
                        <span><?php echo $faq['q']; ?></span>
                        <span class="text-brand-500 transition group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300"><?php echo $faq['a']; ?></p>
                </details>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center justify-between rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-semibold text-white">
            <span>Need onboarding help?</span>
            <a href="about.php" class="rounded-lg bg-white/20 px-3 py-2 hover:bg-white/30">Talk to us</a>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
$modals = join_room_modal();
render_layout('Home', 'home-page', $content, $modals);
?>
