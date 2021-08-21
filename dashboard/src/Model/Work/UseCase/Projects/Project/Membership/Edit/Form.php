<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Membership\Edit;

use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\ReadModel\Work\Projects\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private DepartmentFetcher $departments;
    private RoleFetcher $roles;

    public function __construct(DepartmentFetcher $departments, RoleFetcher $roles)
    {
        $this->departments = $departments;
        $this->roles = $roles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departments', ChoiceType::class, [
                'choices'  => \array_flip($this->departments->listOfProject($options['project'])),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('roles', ChoiceType::class, [
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
