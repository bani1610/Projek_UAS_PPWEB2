<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'lecturer_id'];

    /**
     * Dosen yang membuat grup ini.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Anggota grup.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members');
    }

    /**
     * Pesan dalam grup ini.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
