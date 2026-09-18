<?php

namespace App\Core;

class Upload
{
    /**
     * Handle a mandatory proof-photo upload.
     * Returns the stored filename on success, or throws with a message on failure.
     */
    public static function bukti(array $file, int $userId): string
    {
        if (empty($file) || !isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Bukti foto wajib dilampirkan.');
        }

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            throw new \RuntimeException('Bukti foto wajib dilampirkan.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Terjadi kesalahan saat mengunggah bukti foto.');
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            throw new \RuntimeException('Ukuran bukti foto maksimal 3MB.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) {
            throw new \RuntimeException('Format bukti foto harus JPG, PNG, atau WEBP.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('File yang diunggah bukan gambar yang valid.');
        }

        $userDir = UPLOAD_DIR . '/' . $userId;
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $destination = $userDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Gagal menyimpan bukti foto.');
        }

        return $userId . '/' . $filename;
    }

    public static function delete(string $relativePath): void
    {
        $path = UPLOAD_DIR . '/' . $relativePath;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public static function url(string $relativePath): string
    {
        return UPLOAD_URL . '/' . $relativePath;
    }
}
