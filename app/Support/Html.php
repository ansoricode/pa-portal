<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\File;

/**
 * Membersihkan HTML dari editor teks kaya (berita & halaman) agar aman dari XSS.
 * Hanya tag yang dihasilkan toolbar editor dan penyisip media yang diizinkan; script,
 * style, iframe, atribut on*, dan tautan javascript: dibuang. Atribut class hanya
 * boleh berisi kelas perataan teks dan posisi/ukuran gambar.
 */
class Html
{
    protected static ?HTMLPurifier $purifier = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        return static::purifier()->purify($html);
    }

    protected static function purifier(): HTMLPurifier
    {
        if (static::$purifier === null) {
            $cache = storage_path('app/purifier');
            File::ensureDirectoryExists($cache);

            $config = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('Cache.SerializerPath', $cache);
            $config->set('HTML.Allowed', 'p[class],br,strong,b,em,i,u,s,h2[class],h3[class],h4[class],ul,ol,li[class],blockquote[class],a[href|title|target|rel],img[src|alt|title|width|height|class]');
            $config->set('Attr.AllowedClasses', [
                'ql-align-center', 'ql-align-right', 'ql-align-justify',
                'img-left', 'img-center', 'img-right',
                'img-w-25', 'img-w-50', 'img-w-75', 'img-w-100',
            ]);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('HTML.TargetNoopener', true);
            $config->set('HTML.TargetNoreferrer', true);
            $config->set('AutoFormat.RemoveEmpty', false);

            static::$purifier = new HTMLPurifier($config);
        }

        return static::$purifier;
    }
}
