<?php

namespace App\Model\User\Entity\User;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    public const NAME = 'user_user_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return ($value instanceof Id) ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (null !== $value) ? new Id($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
