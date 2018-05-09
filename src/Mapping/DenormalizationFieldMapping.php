<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

final class DenormalizationFieldMapping implements DenormalizationFieldMappingInterface
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
     * @var string|null
     */
    private $forceType;

    /**
     * @param string                     $name
     * @param array                      $groups
     * @param FieldDenormalizerInterface $fieldDenormalizer
     * @param string|null                $forceType
     */
    public function __construct(
        $name,
        array $groups = [],
        FieldDenormalizerInterface $fieldDenormalizer,
        string $forceType = null
    ) {
        $this->name = $name;
        $this->groups = $groups;
        $this->fieldDenormalizer = $fieldDenormalizer;
        $this->forceType = $forceType;
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

    /**
     * @return string|null
     */
    public function getForceType()
    {
        return $this->forceType;
    }
}
