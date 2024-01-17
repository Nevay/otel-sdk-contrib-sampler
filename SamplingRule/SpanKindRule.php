<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\SamplingRule;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Trace\Span\Kind;
use OpenTelemetry\Context\ContextInterface;
use function sprintf;

/**
 * Checks whether the span kind matches a specific span kind.
 */
final class SpanKindRule implements SamplingRule {

    public function __construct(
        private readonly Kind $spanKind,
    ) {}

    public function matches(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): bool {
        return $this->spanKind === $spanKind;
    }

    public function __toString(): string {
        return sprintf('SpanKind{kind=%s}', $this->spanKind->name);
    }
}
