<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AIFeedbackService;
use App\Models\AI\AIDecision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIFeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $feedbackService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackService = new AIFeedbackService();
    }

    /** @test */
    public function it_can_track_user_feedback()
    {
        $user = User::factory()->create();
        $decision = AIDecision::factory()->create([
            'confidence_score' => 0.85,
        ]);

        $this->actingAs($user);

        $feedback = $this->feedbackService->trackFeedback(
            $decision,
            'accepted',
            'Task analysis was accurate'
        );

        $this->assertNotNull($feedback);
        $this->assertEquals($decision->id, $feedback->decision_id);
        $this->assertEquals('accepted', $feedback->user_action);
    }

    /** @test */
    public function it_calculates_accuracy_correctly()
    {
        // Create test decisions with known outcomes
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 10 decisions: 7 accepted, 3 rejected
        for ($i = 0; $i < 7; $i++) {
            $decision = AIDecision::factory()->create();
            $this->feedbackService->trackFeedback($decision, 'accepted');
        }

        for ($i = 0; $i < 3; $i++) {
            $decision = AIDecision::factory()->create();
            $this->feedbackService->trackFeedback($decision, 'rejected');
        }

        $metrics = $this->feedbackService->getLearningMetrics();

        $this->assertEquals(70, $metrics['accuracy_rate']); // 7/10 = 70%
    }

    /** @test */
    public function it_updates_confidence_calibration()
    {
        $decision = AIDecision::factory()->create([
            'decision_type' => 'priority_adjustment',
            'confidence_score' => 0.9,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        // Accept high-confidence decision
        $this->feedbackService->trackFeedback($decision, 'accepted');
        $this->feedbackService->updateConfidenceCalibration($decision->decision_type);

        $calibration = $this->feedbackService->getCalibrationData($decision->decision_type);

        $this->assertNotNull($calibration);
    }
}
