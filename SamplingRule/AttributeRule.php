<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\SamplingRule;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Trace\Span\Kind;
use OpenTelemetry\Context\ContextInterface;
use function preg_match;
use function sprintf;

/**
 * Checks whether an attribute value matches a regex pattern.
 */
final class AttributeRule implements SamplingRule {

    public function __construct(
        private readonly string $attributeKey,
        private readonly string $pattern,
    ) {}

    public function matches(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): bool {
        return $attributes->has($this->attributeKey)
            && preg_match($this->pattern, (string) $attributes->get($this->attributeKey));
    }

    public function __toString(): string {
        return sprintf('Attribute{key=%s,pattern=%s}', $this->attributeKey, $this->pattern);
    }
}
