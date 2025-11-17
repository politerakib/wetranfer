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
<section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-white via-slate-50 to-brand-50 px-6 py-12 text-slate-900 shadow-xl dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-white">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,_rgba(148,163,184,0.18)_1px,_transparent_0)] bg-[size:22px_22px] opacity-70 dark:opacity-50"></div>
    <div class="relative grid gap-10 lg:grid-cols-2 lg:items-center">
        <div class="space-y-6">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-semibold text-brand-700 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:text-brand-200 dark:ring-slate-700">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                Live P2P rooms · Auto dark/light
            </div>
            <h1 class="text-3xl font-bold leading-tight sm:text-4xl">Design-forward peer-to-peer rooms for rapid collaboration.</h1>
            <p class="max-w-2xl text-lg text-slate-700 dark:text-slate-200">Build, host, and join secure rooms with a refined UI kit. Crafted with Tailwind CSS, responsive layouts, smooth theming, and zero-setup flows.</p>
            <div class="flex flex-wrap gap-3">
                <button data-modal-open="joinRoomModal" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-3 text-base font-semibold text-white shadow-lg hover:shadow-xl">
                    <span>Join Room</span>
                    <span class="text-xs font-medium text-white/90">Instant</span>
                </button>
                <a href="rooms.php" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-base font-semibold text-slate-900 shadow-sm hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:hover:border-brand-400">
                    <span>Create P2P Room</span>
                    <span class="text-xs text-slate-500 dark:text-slate-300">Shareable links</span>
                </a>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Step 1</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Start a room or paste an invite link.</p>
                </div>
                <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Step 2</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Share with teammates via secure tokens.</p>
                </div>
                <div class="rounded-2xl bg-white/80 p-4 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900/70 dark:ring-slate-700">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Step 3</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Chat, handoff files, and switch devices freely.</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white/80 p-6 shadow-2xl ring-1 ring-slate-200 backdrop-blur dark:bg-slate-900/70 dark:ring-slate-700 lg:ml-auto">
            <div class="flex items-center justify-between text-sm text-slate-700 dark:text-slate-200">
                <span>Now in room</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-white dark:ring-slate-700">8 users</span>
            </div>
            <div class="mt-4 space-y-3">
                <?php foreach (['Carla', 'Max', 'Ivy', 'Ren', 'Sage', 'Noor', 'Theo', 'Mara'] as $member): ?>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50/80 px-4 py-3 ring-1 ring-slate-100 dark:bg-slate-800/70 dark:ring-slate-700">
                        <div class="flex items-center space-x-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-lg font-semibold text-white shadow-sm">
                                <?php echo strtoupper($member[0]); ?>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white"><?php echo $member; ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Active now · encrypted</p>
                            </div>
                        </div>
                        <span class="text-xs uppercase tracking-wide text-emerald-600 dark:text-emerald-300">online</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="grid gap-4 rounded-3xl border border-slate-100 bg-white/70 p-6 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/60">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Fast onboarding</p>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Everything teams need to feel guided from click one.</h2>
        </div>
        <a href="about.php" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm hover:border-brand-500 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-800 dark:text-white">View the tour</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Latency</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">&lt;50ms</p>
            <p class="text-slate-600 dark:text-slate-300">Adaptive routing keeps conversations crisp.</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Security</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">End-to-end</p>
            <p class="text-slate-600 dark:text-slate-300">Temporary tokens and encrypted streams by default.</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Onboarding</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">3 clicks</p>
            <p class="text-slate-600 dark:text-slate-300">Join, share, and start collaborating instantly.</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Support</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">24/7</p>
            <p class="text-slate-600 dark:text-slate-300">Guides, FAQs, and concierge onboarding.</p>
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
