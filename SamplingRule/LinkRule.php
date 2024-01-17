<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\SamplingRule;

use Nevay\OtelSDK\Common\Attributes;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Trace\Span\Kind;
use OpenTelemetry\Context\ContextInterface;
use function sprintf;
use function var_export;

/**
 * Checks whether at least one link matches sampled and remote.
 */
final class LinkRule implements SamplingRule {

    public function __construct(
        private readonly bool $sampled,
        private readonly ?bool $remote = null,
    ) {}

    public function matches(
        ContextInterface $context,
        string $traceId,
        string $spanName,
        Kind $spanKind,
        Attributes $attributes,
        array $links,
    ): bool {
        foreach ($links as $link) {
            if ($link->spanContext->isSampled() !== $this->sampled) {
                continue;
            }
            if ($this->remote !== null && $link->spanContext->isRemote() !== $this->remote) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function __toString(): string {
        return sprintf('Link{sampled=%s,remote=%s}', var_export($this->sampled, true), var_export($this->remote, true));
    }
}
