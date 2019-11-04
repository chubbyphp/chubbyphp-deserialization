<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\NullPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

final class DenormalizationFieldMapping implements DenormalizationFieldMappingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @deprecated
     *
     * @var array
     */
    private $groups;

    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @var PolicyInterface
     */
    private $policy;

    /**
     * @param string $name
     */
    public function __construct(
        $name,
        array $groups = [],
        FieldDenormalizerInterface $fieldDenormalizer,
        PolicyInterface $policy = null
    ) {
        $this->name = $name;
        $this->groups = $groups;
        $this->fieldDenormalizer = $fieldDenormalizer;
        $this->policy = $policy ?? new NullPolicy();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getFieldDenormalizer(): FieldDenormalizerInterface
    {
        return $this->fieldDenormalizer;
    }

    public function getPolicy(): PolicyInterface
    {
        return $this->policy;
    }
}
