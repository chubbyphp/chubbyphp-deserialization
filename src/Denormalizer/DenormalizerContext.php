<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @var bool
     */
    private $allowedAdditionalFields = false;

    /**
     * @var string[]
     */
    private $groups = [];

    /**
     * @var ServerRequestInterface|null
     */
    private $request;

    /**
     * @param bool                        $allowedAdditionalFields
     * @param string[]                    $groups
     * @param ServerRequestInterface|null $request
     */
    public function __construct(
        bool $allowedAdditionalFields = false,
        array $groups = [],
        ServerRequestInterface $request = null
    ) {
        $this->allowedAdditionalFields = $allowedAdditionalFields;
        $this->groups = $groups;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isAllowedAdditionalFields(): bool
    {
        return $this->allowedAdditionalFields;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }
}
