<?php
if (!function_exists('startPage')) {
    function startPage(string $title = 'P2P Rooms', bool $loggedIn = true, array $user = []): void
    {
        $pageTitle = $title;
        $isLoggedIn = $loggedIn;
        $userName = $user['name'] ?? 'Nova User';
        $userEmail = $user['email'] ?? 'team@novarooms.com';
        include __DIR__ . '/header.php';
    }
}

if (!function_exists('endPage')) {
    function endPage(): void
    {
        include __DIR__ . '/footer.php';
    }
}

if (!function_exists('renderJoinModal')) {
    function renderJoinModal(): void
    {
        include __DIR__ . '/modal.php';
    }
}
?>
