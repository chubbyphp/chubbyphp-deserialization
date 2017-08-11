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
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/x-yaml';
    }

    /**
     * @param string $string
     *
     * @return array
     *
     * @throws TransformerException
     */
    public function transform(string $string): array
    {
        try {
            return Yaml::parse($string, $this->flags);
        } catch (\TypeError $e) {
            throw TransformerException::create('Yaml not parsable');
        }
    }
}
