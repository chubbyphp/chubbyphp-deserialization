<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @var array|null
     */
    private $allowedAdditionalFields;

    /**
     * @var string[]
     */
    private $groups = [];

    /**
     * @var ServerRequestInterface|null
     */
    private $request;

    /**
     * @var bool
     */
    private $resetMissingFields;

    /**
     * @param array|null                  $allowedAdditionalFields
     * @param string[]                    $groups
     * @param ServerRequestInterface|null $request
     * @param bool                        $resetMissingFields
     */
    public function __construct(
        array $allowedAdditionalFields = null,
        array $groups = [],
        ServerRequestInterface $request = null,
        bool $resetMissingFields = false
    ) {
        $this->allowedAdditionalFields = $allowedAdditionalFields;
        $this->groups = $groups;
        $this->request = $request;
        $this->resetMissingFields = $resetMissingFields;
    }

    /**
     * @return array|null
     */
    public function getAllowedAdditionalFields()
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

    /**
     * @return bool
     */
    public function isResetMissingFields(): bool
    {
        return $this->resetMissingFields;
    }
}
