<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\Configuration;

use Nevay\OtelSDK\Configuration\ComponentProvider;
use Nevay\OtelSDK\Configuration\ComponentProviderRegistry;
use Nevay\OtelSDK\Configuration\Context;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule\SpanKindRule;
use Nevay\OtelSDK\Trace\Span\Kind;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SamplingRuleSpanKind implements ComponentProvider {

    /**
     * @param array{
     *     kind: 'INTERNAL'|'CLIENT'|'SERVER'|'PRODUCER'|'CONSUMER',
     * } $properties
     */
    public function createPlugin(array $properties, Context $context): SamplingRule {
        return new SpanKindRule(
            match ($properties['kind']) {
                'INTERNAL' => Kind::Internal,
                'CLIENT' => Kind::Client,
                'SERVER' => Kind::Server,
                'PRODUCER' => Kind::Producer,
                'CONSUMER' => Kind::Consumer,
            },
        );
    }

    public function getConfig(ComponentProviderRegistry $registry): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('span_kind');
        $node
            ->children()
                ->enumNode('kind')
                    ->isRequired()
                    ->values([
                        'INTERNAL',
                        'CLIENT',
                        'SERVER',
                        'PRODUCER',
                        'CONSUMER',
                    ])
                ->end()
            ->end()
        ;

        return $node;
    }
}
