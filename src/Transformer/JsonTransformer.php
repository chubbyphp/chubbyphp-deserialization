<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

final class JsonTransformer implements TransformerInterface
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
     * @param string $string
     *
     * @return array
     */
    public function transform(string $string): array
    {
        return json_decode($string, true, $this->level, $this->options);
    }
}
