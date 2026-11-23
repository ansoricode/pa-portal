<?php

namespace App\Models\Concerns;

trait HasFileUrl
{
    /** URL berkas unggahan, atau tautan eksternal jika tidak ada berkas. */
    public function getUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }

        return $this->external_url ?: null;
    }
}
