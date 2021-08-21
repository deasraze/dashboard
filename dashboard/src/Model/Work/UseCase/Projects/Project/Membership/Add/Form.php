<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Add;

use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\ReadModel\Work\Projects\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private MemberFetcher $members;
    private RoleFetcher $roles;
    private DepartmentFetcher $departments;

    public function __construct(MemberFetcher $members, RoleFetcher $roles, DepartmentFetcher $departments)
    {
        $this->members = $members;
        $this->roles = $roles;
        $this->departments = $departments;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $members = [];

        foreach ($this->members->activeGroupedList() as $member) {
            $members[$member['group']][$member['name']] = $member['id'];
        }

        $builder
            ->add('member', Type\ChoiceType::class, [
                'choices' => $members
            ])
            ->add('departments', Type\ChoiceType::class, [
                'choices'  => \array_flip($this->departments->listOfProject($options['project'])),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('roles', Type\ChoiceType::class, [
                'choices'  => \array_flip($this->roles->allList()),
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);

        $resolver->setRequired('project');
    }
}
