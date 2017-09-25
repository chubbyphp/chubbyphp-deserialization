<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

interface DenormalizingFieldMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return FieldDenormalizerInterface
     */
    public function getFieldDenormalizer(): FieldDenormalizerInterface;
}
