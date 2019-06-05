<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @method getPolicy(): PolicyInterface
 */
interface DenormalizationFieldMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @deprecated
     *
     * @return array
     */
    public function getGroups(): array;

    /**
     * @return FieldDenormalizerInterface
     */
    public function getFieldDenormalizer(): FieldDenormalizerInterface;

    /*
     * @return PolicyInterface
     */
    //public function getPolicy(): PolicyInterface;
}
