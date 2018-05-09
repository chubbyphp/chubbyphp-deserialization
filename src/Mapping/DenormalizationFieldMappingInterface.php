<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

interface DenormalizationFieldMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getGroups(): array;

    /**
     * @return FieldDenormalizerInterface
     */
    public function getFieldDenormalizer(): FieldDenormalizerInterface;

//    /**
//     * @return string|null
//     */
//    public function getForceType();

    const FORCETYPE_BOOL = 'boolean';
    const FORCETYPE_INT = 'integer';
    const FORCETYPE_FLOAT = 'float';
    const FORCETYPE_STRING = 'string';

    const FORCETYPES = [
        self::FORCETYPE_BOOL,
        self::FORCETYPE_INT,
        self::FORCETYPE_FLOAT,
        self::FORCETYPE_STRING,
    ];
}
