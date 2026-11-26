<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Jurisdiction extends Model
{
    protected $guarded = [];

    /**
     * Daftar desa/kelurahan, satu per baris.
     *
     * @return list<string>
     */
    public function villageList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->villages))));
    }

    /** URL embed (iframe) Google Maps tanpa API key, atau null jika tautan tidak dikenali. */
    protected function mapEmbedUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $url = (string) $this->map_url;

            if (! preg_match('~^https://(www\.google\.com|maps\.google\.com)/maps~', $url)) {
                return null;
            }

            if (str_starts_with($url, 'https://www.google.com/maps/embed')) {
                return $url;
            }

            if (preg_match('~/maps/place/([\w%+.,-]+)~', $url, $m) || preg_match('~[?&](?:q|query)=([\w%+.,-]+)~', $url, $m)) {
                return "https://www.google.com/maps?q={$m[1]}&output=embed";
            }

            if (preg_match('~@(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
                return "https://www.google.com/maps?q={$m[1]},{$m[2]}&output=embed";
            }

            return null;
        });
    }
}
