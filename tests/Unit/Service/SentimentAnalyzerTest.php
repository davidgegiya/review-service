<?php

namespace App\Tests\Unit\Service;

use App\Service\SentimentAnalyzer;
use PHPUnit\Framework\TestCase;
use SentimentAnalysis\Analyzer;

class SentimentAnalyzerTest extends TestCase
{
    private SentimentAnalyzer $analyzer;
    protected function setUp(): void
    {
        parent::setUp();

        $this->analyzer = new SentimentAnalyzer(Analyzer::withDefaultConfig());
    }

    public function testAnalyzerWork() {
        $result = $this->analyzer->analyze("test");
        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(1, $result);
        $this->assertEquals(round($result, 2), $result);
    }

    public function testPositiveScoreCase() {
        $result = $this->analyzer->analyze('Very good movie. Really liked it');
        $this->assertGreaterThan(0.5, $result);
    }

    public function testNegativeScoreCase() {
        $result = $this->analyzer->analyze('Worst movie ever! Absolutely did not liked it');
        $this->assertLessThan(0.5, $result);
    }
}
