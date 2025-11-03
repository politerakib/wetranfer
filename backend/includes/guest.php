<?php

function guest_uid(): string
{
    if (!isset($_SESSION['guest_uid'])) {
        $_SESSION['guest_uid'] = 'guest_' . bin2hex(random_bytes(6));
    }

    return $_SESSION['guest_uid'];
}
