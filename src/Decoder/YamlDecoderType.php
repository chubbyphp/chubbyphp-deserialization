<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Symfony\Component\Yaml\Yaml;

final class YamlDecoderType implements DecoderTypeInterface
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
     * @param string $data
     *
     * @return array
     *
     * @throws DecoderException
     */
    public function decode(string $data): array
    {
        try {
            return Yaml::parse($data, $this->flags);
        } catch (\TypeError $e) {
            throw DecoderException::createNotParsable($this->getContentType());
        }
    }
}
