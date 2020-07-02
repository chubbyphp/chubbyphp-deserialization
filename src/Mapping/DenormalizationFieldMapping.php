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
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @var PolicyInterface
     */
    private $policy;

    public function __construct(
        string $name,
        FieldDenormalizerInterface $fieldDenormalizer,
        ?PolicyInterface $policy = null
    ) {
        $this->name = $name;
        $this->fieldDenormalizer = $fieldDenormalizer;
        $this->policy = $policy ?? new NullPolicy();
    }

    public function getName(): string
    {
        return $this->name;
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
