<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Trace\Sampler;
use Nevay\OtelSDK\Trace\Span\Kind;
use Nevay\OtelSDK\Trace\Span\Link;
use OpenTelemetry\Context\ContextInterface;

interface SamplingRule {

    /**
     * Returns whether this sampling rule matches the given data.
     *
     * @param ContextInterface $context parent context
     * @param string $traceId trace id in binary format
     * @param string $spanName span name
     * @param Kind $spanKind span kind
     * @param Attributes $attributes span attributes
     * @param list<Link> $links span links
     * @return bool whether this rule matches the given data
     *
     * @see Sampler::shouldSample()
     */
    public function matches(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): bool;

    public function __toString(): string;
}
