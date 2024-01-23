<?php declare(strict_types=1);
namespace Nevay\OTelSDK\Contrib\Sampler\Configuration;

use InvalidArgumentException;
use Nevay\OTelSDK\Configuration\ComponentProvider;
use Nevay\OTelSDK\Configuration\ComponentProviderRegistry;
use Nevay\OTelSDK\Configuration\Context;
use Nevay\OTelSDK\Contrib\Sampler\SamplingRule;
use Nevay\OTelSDK\Contrib\Sampler\SamplingRule\SpanNameRule;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use function is_string;
use function preg_match;
use function restore_error_handler;
use function set_error_handler;
use function str_starts_with;
use function strlen;
use function substr;

final class SamplingRuleSpanName implements ComponentProvider {

    /**
     * @param array{
     *     pattern: string,
     * } $properties
     */
    public function createPlugin(array $properties, Context $context): SamplingRule {
        return new SpanNameRule(
            $properties['pattern'],
        );
    }

    public function getConfig(ComponentProviderRegistry $registry): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('span_name');
        $node
            ->children()
                ->scalarNode('pattern')
                    ->isRequired()
                    ->validate()
                        ->always(static function($value): string {
                            if (!is_string($value)) {
                                throw new InvalidArgumentException('must be a regex pattern');
                            }

                            set_error_handler(static fn(int $errno, string $errstr): never
                                => throw new InvalidArgumentException('must be a valid regex pattern: ' . self::stripPrefix($errstr, 'preg_match(): '), $errno));
                            try {
                                preg_match($value, '');
                            } finally {
                                restore_error_handler();
                            }

                            return $value;
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private static function stripPrefix(string $string, string $prefix): string {
        if (str_starts_with($string, $prefix)) {
            return substr($string, strlen($prefix));
        }

        return $string;
    }
}
