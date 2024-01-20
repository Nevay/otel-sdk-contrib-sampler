<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\Configuration;

use Nevay\OtelSDK\Configuration\ComponentPlugin;
use Nevay\OtelSDK\Configuration\ComponentProvider;
use Nevay\OtelSDK\Configuration\ComponentProviderRegistry;
use Nevay\OtelSDK\Configuration\Context;
use Nevay\OtelSDK\Contrib\Sampler\AlwaysRecordingSampler;
use Nevay\OtelSDK\Trace\Sampler;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SamplerAlwaysRecording implements ComponentProvider {

    /**
     * @param array{
     *     sampler: ComponentPlugin<Sampler>
     * } $properties
     */
    public function createPlugin(array $properties, Context $context): Sampler {
        return new AlwaysRecordingSampler(
            $properties['sampler']->create($context),
        );
    }

    public function getConfig(ComponentProviderRegistry $registry): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('always_recording');
        $node
            ->children()
                ->append($registry->component('sampler', Sampler::class)->isRequired())
            ->end()
        ;

        return $node;
    }
}
