<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
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
     * @param string                          $name
     * @param array                           $groups
     * @param FieldDenormalizerInterface|null $fieldDenormalizer
     */
    public function __construct($name, array $groups = [], FieldDenormalizerInterface $fieldDenormalizer = null)
    {
        $this->name = $name;
        $this->fieldDenormalizer = $fieldDenormalizer ?? new FieldDenormalizer(new PropertyAccessor($name));
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
