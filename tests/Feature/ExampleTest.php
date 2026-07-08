<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_la_raiz_redirige_al_login_sin_sesion(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_la_raiz_redirige_al_dashboard_con_sesion(): void
    {
        $bodeguero = $this->makeUser('bodeguero');

        $this->actingAs($bodeguero)->get('/')->assertRedirect(route('dashboard'));
    }
}
