<?php

namespace Tests\Feature\AI;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIIntegrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('manage-ai-settings');
    }

    /** @test */
    public function it_displays_integrations_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/ai/integrations');

        $response->assertStatus(200);
        $response->assertViewIs('admin.ai-integrations.index');
        $response->assertViewHas('health');
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->get('/admin/ai/integrations');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_requires_permission()
    {
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)
            ->get('/admin/ai/integrations');

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_test_local_provider()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/admin/ai/integrations/test-provider', [
                'provider' => 'local',
                'prompt' => 'Test prompt for analysis',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'result' => [
                'provider',
                'response',
                'model',
            ],
        ]);
    }
}
