<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialSurveyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_distinguishes_the_official_badilag_survey_from_internal_feedback(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Survei Resmi Ditjen Badilag', false)
            ->assertSee('href="https://survei.badilag.net/s/c948693eed67bdcfec6ca939896d87b1"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('Beri Masukan untuk Pelayanan Kami')
            ->assertSee('Masukan Internal PTSP &amp; Website', false)
            ->assertSee('Formulir internal portal ini tidak menggantikan survei resmi Ditjen Badilag.', false);
    }
}
