<?php

function storage_store_uploaded_file(array $file, string $roomId, string $uid): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new \RuntimeException('File upload failed with error code ' . ($file['error'] ?? 'unknown'));
    }

    $uploads = __DIR__ . '/../../storage/uploads';
    if (!is_dir($uploads)) {
        mkdir($uploads, 0775, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = sprintf('%s_%s_%s', $roomId, $uid, bin2hex(random_bytes(6)));
    if ($extension) {
        $filename .= '.' . $extension;
    }
    $destination = $uploads . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new \RuntimeException('Unable to move uploaded file.');
    }

    return $filename;
}

function storage_path(string $filename): string
{
    return __DIR__ . '/../../storage/uploads/' . $filename;
}
