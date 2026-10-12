<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    public const MAX_FILE_SIZE_BYTES = 5242880; // 5 MB
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Simpan file foto profil dari UploadedFile atau Base64 Cropped Data.
     *
     * @param UploadedFile|string|null $source File atau base64 string
     * @param string|null $oldPath Path avatar lama di public disk
     * @return string Path relatif file yang disimpan di public disk (e.g. avatars/xyz.jpg)
     * @throws \InvalidArgumentException
     */
    public static function storeAvatar($source, ?string $oldPath = null): string
    {
        if (empty($source)) {
            throw new \InvalidArgumentException('Tidak ada data foto yang diterima untuk diproses.');
        }

        // Buat folder avatars jika belum ada
        if (!Storage::disk('public')->exists('avatars')) {
            Storage::disk('public')->makeDirectory('avatars');
        }

        $filename = 'avatar_' . Str::random(20) . '_' . time();
        $savedPath = null;

        if ($source instanceof UploadedFile) {
            // Validasi ukuran & tipe file
            if ($source->getSize() > self::MAX_FILE_SIZE_BYTES) {
                throw new \InvalidArgumentException('Ukuran file melebihi batas maksimal 5 MB.');
            }

            $mime = strtolower((string) $source->getMimeType());
            $ext = strtolower($source->getClientOriginalExtension());
            if (!in_array($mime, self::ALLOWED_MIME_TYPES) && !in_array($ext, self::ALLOWED_EXTENSIONS)) {
                throw new \InvalidArgumentException('Format file tidak didukung. Harap gunakan format JPG, JPEG, PNG, atau WebP.');
            }

            $extension = in_array($ext, self::ALLOWED_EXTENSIONS) ? $ext : 'jpg';
            $savedPath = $source->storeAs('avatars', "{$filename}.{$extension}", 'public');

            // Salin juga ke public/storage/avatars jika direktori publik tersedia (dukungan hosting tanpa symlink)
            try {
                $publicFolder = public_path('storage/avatars');
                if (!file_exists($publicFolder)) {
                    @mkdir($publicFolder, 0775, true);
                }
                @copy($source->getRealPath(), $publicFolder . '/' . "{$filename}.{$extension}");
            } catch (\Throwable $e) {
                // Abaikan jika direktori publik memiliki batasan izin
            }
        } elseif (is_string($source) && str_starts_with($source, 'data:image/')) {
            // Base64 Cropped Image dari Canvas
            if (!preg_match('/^data:image\/(\w+);base64,/', $source, $type)) {
                throw new \InvalidArgumentException('Format data gambar tidak valid.');
            }

            $extension = strtolower($type[1]);
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }

            if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
                throw new \InvalidArgumentException('Format data gambar harus JPG, JPEG, PNG, atau WebP.');
            }

            $data = substr($source, strpos($source, ',') + 1);
            $decodedData = base64_decode($data);

            if ($decodedData === false) {
                throw new \InvalidArgumentException('Gagal memproses data gambar.');
            }

            if (strlen($decodedData) > self::MAX_FILE_SIZE_BYTES) {
                throw new \InvalidArgumentException('Ukuran hasil pemotongan gambar melebihi batas maksimal 5 MB.');
            }

            $fullPath = "avatars/{$filename}.{$extension}";
            Storage::disk('public')->put($fullPath, $decodedData);
            $savedPath = $fullPath;

            // Salin juga ke public/storage/avatars jika direktori publik tersedia (dukungan hosting tanpa symlink)
            try {
                $publicFolder = public_path('storage/avatars');
                if (!file_exists($publicFolder)) {
                    @mkdir($publicFolder, 0775, true);
                }
                @file_put_contents($publicFolder . '/' . "{$filename}.{$extension}", $decodedData);
            } catch (\Throwable $e) {
                // Abaikan jika direktori publik memiliki batasan izin
            }
        } else {
            throw new \InvalidArgumentException('Format input foto tidak dikenali.');
        }

        // Hapus foto lama jika ada dan bukan avatar default
        self::deleteOldAvatar($oldPath);

        return $savedPath;
    }

    /**
     * Hapus foto avatar lama dari public disk storage jika ada.
     */
    public static function deleteOldAvatar(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Hapus dari Storage disk public
        if (str_starts_with($path, 'avatars/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Hapus juga dari public_path jika ada
        try {
            $publicFile = public_path('storage/' . $path);
            if (file_exists($publicFile)) {
                @unlink($publicFile);
            }
        } catch (\Throwable $e) {
            // Abaikan
        }

        return true;
    }

    /**
     * Dapatkan URL avatar lengkap (URL storage jika custom, atau URL default avatar jika belum ada/dihapus).
     */
    public static function getAvatarUrl(?string $path, string $role = 'siswa'): string
    {
        if (!empty($path) && (Storage::disk('public')->exists($path) || file_exists(public_path('storage/' . $path)))) {
            return asset('storage/' . $path);
        }

        return $role === 'guru_bk'
            ? asset('images/default-avatar-guru.svg')
            : asset('images/default-avatar-student.svg');
    }

    /**
     * Cek apakah user/siswa memiliki custom avatar yang valid.
     */
    public static function hasCustomAvatar(?string $path): bool
    {
        return !empty($path) && (Storage::disk('public')->exists($path) || file_exists(public_path('storage/' . $path)));
    }
}
