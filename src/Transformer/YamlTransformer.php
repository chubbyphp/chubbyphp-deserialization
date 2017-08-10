<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

use Symfony\Component\Yaml\Yaml;

final class YamlTransformer implements TransformerInterface
{
    /**
     * @var int
     */
    private $flags;

    /**
     * @param int $flags
     */
    public function __construct(int $flags = 0)
    {
        $this->flags = $flags;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    public function transform(string $string): array
    {
        return Yaml::parse($string, $this->flags);
    }
}
