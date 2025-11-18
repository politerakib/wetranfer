<?php
require_once __DIR__ . '/components/layout.php';

$messages = [
    ['type' => 'info', 'time' => '09:23', 'text' => 'Ren joined the room securely'],
    ['type' => 'incoming', 'user' => 'Jordan', 'time' => '09:24', 'text' => 'Welcome to the premium chat room!'],
    ['type' => 'outgoing', 'user' => 'Sage', 'time' => '09:26', 'text' => 'Latency is almost zero here and files stay encrypted.'],
    ['type' => 'incoming', 'user' => 'Ren', 'time' => '09:27', 'text' => 'Loving the dark/light switch — looks sharp on mobile too.'],
    ['type' => 'outgoing', 'user' => 'Sage', 'time' => '09:28', 'text' => 'Dropping the sprint brief now.'],
];

$users = [
    ['name' => 'Jordan Lee', 'status' => 'online'],
    ['name' => 'Sage Bloom', 'status' => 'online'],
    ['name' => 'Ren Ito', 'status' => 'away'],
    ['name' => 'Max Star', 'status' => 'online'],
];

ob_start();
?>
<section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Live room</p>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Design Studio · p2p-8931</h1>
            <p class="text-slate-600 dark:text-slate-300">Encrypted chat, presence, and clean controls.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:border-brand-400 dark:border-slate-700 dark:bg-slate-800 dark:text-white">Share link</button>
            <button class="rounded-lg bg-gradient-to-r from-brand-500 to-brand-600 px-3 py-2 text-sm font-semibold text-white shadow">Invite</button>
        </div>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-3xl border border-slate-100 bg-white shadow-md dark:border-slate-800 dark:bg-slate-800/70">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 text-sm text-slate-600 dark:border-slate-700 dark:text-slate-200">
                <span>Conversation</span>
                <span class="flex items-center space-x-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-white">Live</span>
            </div>
            <div class="h-[420px] space-y-4 overflow-y-auto px-6 py-5 bg-slate-50 dark:bg-slate-900/60">
                <?php foreach ($messages as $message): ?>
                    <?php if ($message['type'] === 'info'): ?>
                        <div class="flex justify-center">
                            <div class="flex items-center gap-2 rounded-full bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-300 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                <?php echo $message['text']; ?> · <?php echo $message['time']; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $isSelf = $message['type'] === 'outgoing'; ?>
                        <div class="flex <?php echo $isSelf ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-xl rounded-2xl px-4 py-3 shadow-sm <?php echo $isSelf ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white' : 'bg-white text-slate-900 dark:bg-slate-800 dark:text-slate-50'; ?>">
                                <div class="flex items-center justify-between text-xs <?php echo $isSelf ? 'text-white/80' : 'text-slate-500 dark:text-slate-300'; ?>">
                                    <span class="font-semibold"><?php echo $message['user']; ?></span>
                                    <span><?php echo $message['time']; ?></span>
                                </div>
                                <p class="mt-2 text-sm <?php echo $isSelf ? 'text-white' : 'text-slate-700 dark:text-slate-200'; ?>"><?php echo $message['text']; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-3 border-t border-slate-100 px-6 py-4 dark:border-slate-700">
                <input type="text" placeholder="Type a message" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
                <button class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg">➤</button>
            </div>
        </div>
        <aside class="space-y-4 rounded-3xl border border-slate-100 bg-white p-5 shadow-md dark:border-slate-800 dark:bg-slate-800/70">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Users</p>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white"><?php echo count($users); ?> joined</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-white">Secure</span>
            </div>
            <div class="space-y-3">
                <?php foreach ($users as $user): ?>
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/50">
                        <div class="flex items-center space-x-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-white font-semibold"><?php echo strtoupper($user['name'][0]); ?></span>
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white"><?php echo $user['name']; ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Status: <?php echo $user['status']; ?></p>
                            </div>
                        </div>
                        <span class="h-2 w-2 rounded-full <?php echo $user['status'] === 'online' ? 'bg-emerald-400' : 'bg-amber-300'; ?>"></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</section>
<?php
$content = ob_get_clean();
render_layout('Room', 'room-page', $content);
?>
