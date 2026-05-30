<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParishContent extends Model
{
    private const PROTECTED_KEY_ALIASES = [
        'hero',
        'inicio',
        'mass_schedule',
        'horarios',
        'misas',
        'sacraments',
        'sacramentos',
        'news',
        'noticias',
        'community',
        'comunidad',
        'contact',
        'contacto',
    ];

    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'body',
        'highlights',
        'cta_text',
        'cta_url',
        'use_detail_page',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'use_detail_page' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(ParishContentImage::class)->orderBy('display_order');
    }

    public static function protectedKeys(): array
    {
        return self::PROTECTED_KEY_ALIASES;
    }

    public function isProtected(): bool
    {
        return in_array($this->key, self::protectedKeys(), true);
    }
}
