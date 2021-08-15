<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
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
            ->setChildrenAttribute('class', 'nav');

        $menu->addChild('Dashboard', ['route' => 'home'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('icon', 'nav-icon icon-speedometer');

        $menu->addChild('Work')->setAttribute('class', 'nav-title');

        if ($this->auth->isGranted('ROLE_WORK_MANAGE_MEMBERS')) {
            $menu->addChild('Members', ['route' => 'work.members'])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link')
                ->setExtra('icon', 'nav-icon icon-people')
                ->setExtra('routes', [
                    ['route' => 'work.members'],
                    ['pattern' => '/^work\.members\..+/']
                ]);
        }

        $menu->addChild('Control')->setAttribute('class', 'nav-title');

        if ($this->auth->isGranted('ROLE_MANAGE_USERS')) {
            $menu->addChild('Users', ['route' => 'users'])
                ->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link')
                ->setExtra('icon', 'nav-icon icon-people')
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..+/']
                ]);
        }

        $menu->addChild('Profile', ['route' => 'profile'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link')
            ->setExtra('icon', 'nav-icon icon-user')
            ->setExtra('routes', [
                ['route' => 'profile'],
                ['pattern' => '/^profile\..+/']
            ]);

        return $menu;
    }
}
