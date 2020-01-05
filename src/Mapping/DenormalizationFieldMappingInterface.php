<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @method PolicyInterface getPolicy()
 */
interface DenormalizationFieldMappingInterface
{
    public function getName(): string;

    /**
     * @deprecated
     *
     * @return array<int, string>
     */
    public function getGroups(): array;

    public function getFieldDenormalizer(): FieldDenormalizerInterface;

    /*
     * @return PolicyInterface
     */
    //public function getPolicy(): PolicyInterface;
}
