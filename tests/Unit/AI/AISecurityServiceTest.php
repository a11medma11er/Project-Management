<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AISecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AISecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $securityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->securityService = new AISecurityService();
    }

    /** @test */
    public function it_sanitizes_input_correctly()
    {
        $input = '<script>alert("XSS")</script>Hello World';
        $sanitized = $this->securityService->sanitizeInput($input);

        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertEquals('Hello World', $sanitized);
    }

    /** @test */
    public function it_detects_sql_injection_attempts()
    {
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "admin'--",
        ];

        foreach ($maliciousInputs as $input) {
            $isSuspicious = $this->securityService->detectSuspiciousActivity([$input]);
            $this->assertTrue($isSuspicious, "Failed to detect: {$input}");
        }
    }

    /** @test */
    public function it_detects_xss_attempts()
    {
        $maliciousInputs = [
            '<script>alert("XSS")</script>',
            'javascript:alert(1)',
            '<img onerror="alert(1)">',
        ];

        foreach ($maliciousInputs as $input) {
            $isSuspicious = $this->securityService->detectSuspiciousActivity([$input]);
            $this->assertTrue($isSuspicious, "Failed to detect XSS: {$input}");
        }
    }

    /** @test */
    public function it_validates_decision_data()
    {
        $validData = [
            'decision_type' => 'priority_adjustment',
            'entity_type' => 'task',
            'entity_id' => 1,
            'confidence_score' => 0.85,
        ];

        $errors = $this->securityService->validateDecisionData($validData);
        $this->assertEmpty($errors);

        $invalidData = [
            'decision_type' => 'invalid_type',
            'confidence_score' => 1.5, // Out of range
        ];

        $errors = $this->securityService->validateDecisionData($invalidData);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function it_allows_safe_input()
    {
        $safeInput = 'This is a normal task description';
        
        $isSuspicious = $this->securityService->detectSuspiciousActivity([$safeInput]);
        $this->assertFalse($isSuspicious);
        
        $sanitized = $this->securityService->sanitizeInput($safeInput);
        $this->assertEquals($safeInput, $sanitized);
    }
}
