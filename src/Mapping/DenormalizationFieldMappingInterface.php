<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

interface DenormalizationFieldMappingInterface
{
    public function getName(): string;

    public function getFieldDenormalizer(): FieldDenormalizerInterface;

    public function getPolicy(): PolicyInterface;
}
