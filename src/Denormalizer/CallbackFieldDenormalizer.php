<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

final class CallbackFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @var array
     */
    private $groups;

    /**
     * @param callable $callback
     * @param array    $groups
     */
    public function __construct(callable $callback, array $groups = [])
    {
        $this->callback = $callback;
        $this->groups = $groups;
    }

    /**
     * @param string                            $path
     * @param object                            $object
     * @param mixed                             $value
     * @param DenormalizerInterface|null        $denormalizer
     * @param DenormalizerContextInterface|null $context
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerInterface $denormalizer = null,
        DenormalizerContextInterface $context = null
    ) {
        $callback = $this->callback;

        $callback($path, $object, $value, $denormalizer, $context);
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
