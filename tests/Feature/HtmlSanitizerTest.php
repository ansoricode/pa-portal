<?php

namespace Tests\Feature;

use App\Support\Html;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_keeps_alignment_and_image_position_classes(): void
    {
        $clean = Html::clean('<p class="ql-align-center">Teks</p><p><img src="/storage/a.jpg" class="img-right img-w-50" alt="a"></p>');

        $this->assertStringContainsString('class="ql-align-center"', $clean);
        $this->assertStringContainsString('img-right', $clean);
        $this->assertStringContainsString('img-w-50', $clean);
    }

    public function test_it_drops_unknown_classes_and_inline_styles(): void
    {
        $clean = Html::clean('<p class="evil ql-align-right" style="color:red">Teks</p><img src="/storage/a.jpg" class="img-selected" onclick="x()">');

        $this->assertStringNotContainsString('evil', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('img-selected', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('ql-align-right', $clean);
    }
}
