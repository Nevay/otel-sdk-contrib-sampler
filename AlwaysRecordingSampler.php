<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Trace\Sampler;
use Nevay\OtelSDK\Trace\SamplingResult;
use Nevay\OtelSDK\Trace\Span\Kind;
use OpenTelemetry\Context\ContextInterface;
use function sprintf;

/**
 * Records all spans to allow the usage of span processors that generate metrics from spans.
 */
final class AlwaysRecordingSampler implements Sampler {

    public function __construct(
        private readonly Sampler $sampler,
    ) {}

    public function shouldSample(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): SamplingResult {
        $result = $this->sampler->shouldSample($context, $traceId, $spanName, $spanKind, $attributes, $links);
        if (!$result->shouldRecord()) {
            $result = new AlwaysRecordingSamplingResult($result);
        }

        return $result;
    }

    public function __toString(): string {
        return sprintf('AlwaysRecordingSampler{%s}', $this->sampler);
    }
}
