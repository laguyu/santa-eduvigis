<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParishContentImage extends Model
{
    protected $fillable = [
        'parish_content_id',
        'path',
        'caption',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(ParishContent::class, 'parish_content_id');
    }
}
