<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkClassMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'bk_class_id',
        'title',
        'description',
        'material_type',
        'file_path',
        'file_name',
        'file_size',
        'video_url',
        'order_num',
    ];

    public function bkClass()
    {
        return $this->belongsTo(BkClass::class, 'bk_class_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->material_type) {
            'slide' => 'Slide Pembelajaran',
            'video' => 'Video Pembelajaran',
            'document' => 'Dokumen Materi',
            default => 'Materi',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 1) . ' MB';
        }

        return number_format($this->file_size / 1024, 0) . ' KB';
    }

    /**
     * Konversi link video (misal YouTube) ke URL embed iframe yang aman
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }
}
