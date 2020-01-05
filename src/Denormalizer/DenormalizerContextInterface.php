<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @method array                        getAttributes()
 * @method mixed                        getAttribute(string $name, $default = null)
 * @method DenormalizerContextInterface withAttribute(string $name, $value)
 */
interface DenormalizerContextInterface
{
    /**
     * @return array<int, string>|null
     */
    public function getAllowedAdditionalFields();

    /**
     * @deprecated
     *
     * @return array<int, string>
     */
    public function getGroups(): array;

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest();

    /*
     * @return array
     */
    //public function getAttributes(): array;

    /*
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    //public function getAttribute(string $name, $default = null);

    /*
     * @param string $name
     * @param mixed  $value
     * @return self
     */
    //public function withAttribute(string $name, $value): self;
}
