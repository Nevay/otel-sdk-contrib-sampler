<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Trace\Sampler;
use Nevay\OtelSDK\Trace\SamplingResult;
use Nevay\OtelSDK\Trace\Span\Kind;
use OpenTelemetry\Context\ContextInterface;
use function implode;
use function sprintf;

/**
 * Samples based on a list of rule sets. The first matching rule set will be
 * used for sampling decisions.
 */
final class RuleBasedSampler implements Sampler {

    /**
     * @param list<RuleSet> $ruleSets
     */
    public function __construct(
        private readonly array $ruleSets,
        private readonly Sampler $fallback,
    ) {}

    public function shouldSample(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): SamplingResult {
        foreach ($this->ruleSets as $ruleSet) {
            foreach ($ruleSet->samplingRules as $samplingRule) {
                if (!$samplingRule->matches($context, $traceId, $spanName, $spanKind, $attributes, $links)) {
                    continue 2;
                }
            }

            return $ruleSet->delegate->shouldSample($context, $traceId, $spanName, $spanKind, $attributes, $links);
        }

        return $this->fallback->shouldSample($context, $traceId, $spanName, $spanKind, $attributes, $links);
    }

    public function __toString(): string {
        return sprintf('RuleBasedSampler{rules=[%s],fallback=%s}', implode(',', $this->ruleSets), $this->fallback);
    }
}
