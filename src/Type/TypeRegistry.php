<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

use Chubbyphp\Deserialization\DeserializerLogicException;

final class TypeRegistry implements TypeRegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    private $types = [];

    public function __construct(array $types)
    {
        foreach ($types as $type) {
            $this->addType($type);
        }
    }

    /**
     * @param TypeInterface $type
     */
    private function addType(TypeInterface $type)
    {
        $this->types[$type->getType()] = $type;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     *
     * @throws DeserializerLogicException
     */
    public function convert(string $type, $value)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type]->convert($value);
        }

        throw DeserializerLogicException::createMissingType($type);
    }
}
