<?php declare(strict_types=1);
namespace Nevay\OtelSDK\Contrib\Sampler\Configuration;

use Nevay\OtelSDK\Configuration\ComponentPlugin;
use Nevay\OtelSDK\Configuration\ComponentProvider;
use Nevay\OtelSDK\Configuration\ComponentProviderRegistry;
use Nevay\OtelSDK\Configuration\Context;
use Nevay\OtelSDK\Contrib\Sampler\RuleBasedSampler;
use Nevay\OtelSDK\Contrib\Sampler\RuleSet;
use Nevay\OtelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OtelSDK\Trace\Sampler;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SamplerRuleBased implements ComponentProvider {

    /**
     * @param array{
     *     rule_sets: list<array{
     *         rules: list<ComponentPlugin<SamplingRule>>,
     *         delegate: ComponentPlugin<Sampler>,
     *     }>,
     *     fallback: ComponentPlugin<Sampler>,
     * } $properties
     */
    public function createPlugin(array $properties, Context $context): Sampler {
        $ruleSets = [];
        foreach ($properties['rule_sets'] as $ruleSet) {
            $samplingRules = [];
            foreach ($ruleSet['rules'] as $rule) {
                $samplingRules[] = $rule->create($context);
            }

            $ruleSets[] = new RuleSet(
                $samplingRules,
                $ruleSet['delegate']->create($context),
            );
        }

        return new RuleBasedSampler(
            $ruleSets,
            $properties['fallback']->create($context),
        );
    }

    public function getConfig(ComponentProviderRegistry $registry): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('rule_based');
        $node
            ->children()
                ->arrayNode('rule_sets')
                    ->arrayPrototype()
                        ->children()
                            ->append($registry->componentList('rules', SamplingRule::class)->isRequired()->cannotBeEmpty())
                            ->append($registry->component('delegate', Sampler::class)->isRequired())
                        ->end()
                    ->end()
                ->end()
                ->append($registry->component('fallback', Sampler::class)->isRequired())
            ->end()
        ;

        return $node;
    }
}
