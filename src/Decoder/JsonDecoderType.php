<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class JsonDecoderType implements DecoderTypeInterface
{
    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $options;

    /**
     * JsonTransformer constructor.
     *
     * @param int $options
     * @param int $level
     */
    public function __construct(int $level = 512, int $options = 0)
    {
        $this->options = $options;
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
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
            return json_decode($data, true, $this->level, $this->options);
        } catch (\TypeError $e) {
            throw DecoderException::createNotParsable($this->getContentType());
        }
    }
}
