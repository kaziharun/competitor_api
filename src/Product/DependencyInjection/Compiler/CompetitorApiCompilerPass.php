<?php

declare(strict_types=1);

namespace App\Product\DependencyInjection\Compiler;

use App\Product\Infrastructure\Api\CompetitorApiFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register competitor APIs with the factory.
 */
final class CompetitorApiCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CompetitorApiFactory::class)) {
            return;
        }

        $factoryDefinition = $container->findDefinition(CompetitorApiFactory::class);
        $taggedServices = $container->findTaggedServiceIds('app.competitor_api');

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $alias = $tag['alias'] ?? $serviceId;

                $factoryDefinition->addMethodCall('register', [
                    $alias,
                    new Reference($serviceId),
                ]);
            }
        }
    }
}
