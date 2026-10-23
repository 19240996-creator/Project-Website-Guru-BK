<?php

namespace App\Services\Security;

class SecurePath
{
    /**
     * Token hash rute terenkripsi / terobfuskasi untuk URL akun pemantau
     * (Admin DICO & Kepala Kampus) agar tidak terdeteksi oleh scanner otomatis.
     */
    public const DASHBOARD = 'e-d9a4b27f';
    public const SISWA = 'e-s8f1c39a';
    public const EKSPOR = 'e-x2e7b41d';
    public const EKSPOR_COUNT = 'preview-count';
    public const EKSPOR_DOWNLOAD = 'unduh';
    public const LOG = 'e-l5a3c98f';
    public const PROFIL = 'e-p7d1f62b';
    public const PROFIL_PHOTO = 'foto';

    /**
     * Enkripsi ID siswa menjadi token URL-safe bertanda tangan HMAC.
     */
    public static function encryptId(int|string $id): string
    {
        $idStr = (string) $id;
        $salt = config('app.key') ?: 'RuangBK_Secret_Salt_2026';
        $signature = substr(hash_hmac('sha256', $idStr, $salt), 0, 10);
        $payload = $idStr . '.' . $signature;
        
        return 'id-' . strtr(base64_encode($payload), '+/=', '-_~');
    }

    /**
     * Dekripsi token URL kembali menjadi ID integer siswa.
     * Mengembalikan null jika token tidak valid atau telah dimanipulasi.
     */
    public static function decryptId(string $token): ?int
    {
        if (str_starts_with($token, 'id-')) {
            $raw = substr($token, 3);
            $decoded = base64_decode(strtr($raw, '-_~', '+/='));
            if ($decoded && str_contains($decoded, '.')) {
                [$idStr, $signature] = explode('.', $decoded, 2);
                $salt = config('app.key') ?: 'RuangBK_Secret_Salt_2026';
                $expected = substr(hash_hmac('sha256', $idStr, $salt), 0, 10);
                if (hash_equals($expected, $signature) && is_numeric($idStr)) {
                    return (int) $idStr;
                }
            }
        }

        return null;
    }

    /**
     * Resolusi ID siswa: mendukung token terenkripsi maupun ID numerik langsung.
     */
    public static function resolveId(int|string $idOrToken): ?int
    {
        if (is_numeric($idOrToken)) {
            return (int) $idOrToken;
        }

        return self::decryptId((string) $idOrToken);
    }
}
