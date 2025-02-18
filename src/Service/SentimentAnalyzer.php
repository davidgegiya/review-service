<?php

namespace App\Service;

use SentimentAnalysis\Analyzer;

class SentimentAnalyzer
{
    private Analyzer $analyzer;
    const PRECISION = 2;
    public function __construct(Analyzer $analyzer) {
        $this->analyzer = $analyzer;
    }
    public function analyze(string $text): float
    {
        $result = $this->analyzer->analyze($text);
        $score = $result->scores();

        $positive = $score['positive'] ?? 0;
        $negative = $score['negative'] ?? 0;

        // Count weighted average score
        return round(($positive - $negative + 1) / 2, self::PRECISION);
    }
}