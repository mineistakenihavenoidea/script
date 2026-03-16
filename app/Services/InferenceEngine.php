<?php

namespace App\Services;

class InferenceEngine
{
    /**
     * @var array<array{condition: callable, action: callable}>
     */
    private array $rules = [];

    /**
     * @var array<string, mixed>
     */
    private array $facts = [];

    /**
     * Add a rule to the inference engine.
     *
     * @param  callable  $condition  Rule condition that returns bool
     * @param  callable  $action  Action to execute when condition is true
     */
    public function addRule(callable $condition, callable $action): self
    {
        $this->rules[] = [
            'condition' => $condition,
            'action' => $action,
        ];

        return $this;
    }

    /**
     * Set a fact in the knowledge base.
     */
    public function setFact(string $key, mixed $value): self
    {
        $this->facts[$key] = $value;

        return $this;
    }

    /**
     * Get a fact from the knowledge base.
     */
    public function getFact(string $key): mixed
    {
        return $this->facts[$key] ?? null;
    }

    /**
     * Check if a fact exists.
     */
    public function hasFact(string $key): bool
    {
        return isset($this->facts[$key]);
    }

    /**
     * Execute forward chaining inference.
     *
     * @return array<int, mixed>  Results from actions that fired
     */
    public function infer(): array
    {
        $results = [];
        $fired = true;

        while ($fired) {
            $fired = false;

            foreach ($this->rules as $rule) {
                $condition = $rule['condition'];

                if ($condition($this->facts)) {
                    $action = $rule['action'];
                    $result = $action($this->facts);
                    $results[] = $result;
                    $fired = true;
                }
            }
        }

        return $results;
    }

    /**
     * Reset the inference engine.
     */
    public function reset(): self
    {
        $this->rules = [];
        $this->facts = [];

        return $this;
    }

/**
 * Score a domain based on yes/no responses.
 *
 * @param  array<bool>  $responses  Array of boolean responses
 * @return array{score: int, percentage: float, classification: string}
 */

public function scoreDomain(array $responses): array
{
    $yesCount = count(array_filter($responses));
    $totalQuestions = count($responses);
    $percentage = $totalQuestions > 0 ? ($yesCount / $totalQuestions) * 100 : 0;

    $classification = match (true) {
        $percentage >= 80 => 'good',
        $percentage >= 60 => 'average',
        default => 'bad',
    };

    return [
        'score' => $yesCount,
        'percentage' => $percentage,
        'classification' => $classification,
    ];
}

/**
 * Infer recommendations based on domain classifications.
 *
 * @param  array<string, string>  $domainClassifications  Domain => classification mapping
 * @return array{status: string, recommendation: ?string}
 */

public function inferRecommendation(array $domainClassifications): array
{
    $classifications = array_values($domainClassifications);
    $badCount = count(array_filter($classifications, fn ($c) => $c === 'bad'));
    $averageCount = count(array_filter($classifications, fn ($c) => $c === 'average'));

    if ($badCount > 0) {
        return [
            'status' => 'needs_tutor',
            'recommendation' => 'Advise to get a tutor for areas needing improvement',
        ];
    }

    if ($averageCount > 0) {
        return [
            'status' => 'needs_recommendation',
            'recommendation' => 'Provide targeted recommendations for average domains',
        ];
    }

    return [
        'status' => 'good',
        'recommendation' => null,
    ];
}
}