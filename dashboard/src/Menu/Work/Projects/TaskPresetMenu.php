<?php

declare(strict_types=1);

namespace App\Menu\Work\Projects;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class TaskPresetMenu
{
    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-tabs mb-4');

        $route = $options['project_id'] ? 'work.projects.project.tasks' : 'work.projects.tasks';
        $menu
            ->addChild('All tasks', [
                'route' => $route,
                'routeParameters' => $options['route_params'],
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('routes', [['route' => $route]]);

        $route = $options['project_id'] ? 'work.projects.project.tasks.me' : 'work.projects.tasks.me';
        $menu
            ->addChild('For Me', [
                'route' => $route,
                'routeParameters' => \array_replace_recursive($options['route_params'], ['form' => ['executor' => null]]),
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('routes', [['route' => $route]]);

        $route = $options['project_id'] ? 'work.projects.project.tasks.own' : 'work.projects.tasks.own';
        $menu
            ->addChild('My Own', [
                'route' => $route,
                'routeParameters' => \array_replace_recursive($options['route_params'], ['form' => ['author' => null]]),
            ])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('routes', [['route' => $route]]);

        return $menu;
    }
}
