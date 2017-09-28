<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

final class DenormalizingFieldMapping implements DenormalizingFieldMappingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @param string                     $name
     * @param array                      $groups
     * @param FieldDenormalizerInterface $fieldDenormalizer
     */
    public function __construct($name, array $groups = [], FieldDenormalizerInterface $fieldDenormalizer)
    {
        $this->name = $name;
        $this->groups = $groups;
        $this->fieldDenormalizer = $fieldDenormalizer;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return FieldDenormalizerInterface
     */
    public function getFieldDenormalizer(): FieldDenormalizerInterface
    {
        return $this->fieldDenormalizer;
    }
}
