<?php

declare(strict_types=1);

namespace App\Container\Work;

use App\Twig\Extension\Work\Processor\ProcessorExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ProcessorExtension::class)) {
            return;
        }

        $definition = $container->findDefinition(ProcessorExtension::class);

        $services = $container->findTaggedServiceIds('app.twig.work_processor.driver');

        $references = [];

        foreach ($services as $id => $tags) {
            $references[] = new Reference($id);
        }

        $definition->setArgument(0, $references);
    }
}
