<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\Configuration;

use Nevay\OtelSDK\Configuration\ComponentProvider;
use Nevay\OtelSDK\Configuration\ComponentProviderRegistry;
use Nevay\OtelSDK\Configuration\Context;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule\LinkRule;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SamplingRuleLink implements ComponentProvider {

    /**
     * @param array{
     *     sampled: bool,
     *     remote: ?bool,
     * } $properties
     */
    public function createPlugin(array $properties, Context $context): SamplingRule {
        return new LinkRule(
            $properties['sampled'],
            $properties['remote'],
        );
    }

    public function getConfig(ComponentProviderRegistry $registry): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('link');
        $node
            ->children()
                ->booleanNode('sampled')->isRequired()->end()
                ->booleanNode('remote')->defaultNull()->end()
            ->end()
        ;

        return $node;
    }
}
