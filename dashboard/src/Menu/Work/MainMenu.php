<?php

declare(strict_types=1);

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenu
{
    private FactoryInterface $factory;
    private AuthorizationCheckerInterface $auth;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-tabs mb-4');

        $menu
            ->addChild('Projects', ['route' => 'work.projects'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('routes', [
                ['route' => 'work.projects'],
                ['route' => 'work.projects.create'],
            ]);

        $menu
            ->addChild('Actions', ['route' => 'work.projects.actions'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        $menu
            ->addChild('Tasks', ['route' => 'work.projects.tasks'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('routes', [
                ['route' => 'work.projects.tasks'],
                ['pattern' => '/^work\.projects\.tasks\..+/'],
            ]);

        $menu
            ->addChild('Calendar', ['route' => 'work.projects.calendar'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $menu
                ->addChild('Roles', ['route' => 'work.projects.roles'])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link')
                ->setExtra('routes', [
                    ['route' => 'work.projects.roles'],
                    ['pattern' => '/^work\.projects\.roles\..+/'],
                ]);
        }

        return $menu;
    }
}
