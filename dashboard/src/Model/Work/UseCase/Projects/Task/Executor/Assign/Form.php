<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use App\ReadModel\Work\Members\Member\MemberFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private MemberFetcher $members;

    public function __construct(MemberFetcher $members)
    {
        $this->members = $members;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $members = [];

        foreach ($this->members->activeDepartmentListForProject($options['project_id']) as $member) {
            $members[$member['department'] . ' - ' . $member['name']] = $member['id'];
        }

        $builder
            ->add('members', ChoiceType::class, [
                'choices' => $members,
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);

        $resolver->setRequired('project_id');
    }
}
