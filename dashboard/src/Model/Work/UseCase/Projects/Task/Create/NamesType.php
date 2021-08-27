<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class NamesType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return \implode(PHP_EOL, \array_map(static function (NameRow $nameRow) {
            return $nameRow->name;
        }, $value));
    }

    public function reverseTransform($value): array
    {
        return \array_filter(\array_map(static function (string $name) {
            if (empty($name)) {
                return null;
            }

            return new NameRow($name);
        }, \preg_split('#[\r\n]+#', $value)));
    }
}
