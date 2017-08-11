<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Transformer\TransformerInterface as ContenTypeTransformerInterface;

final class Transformer implements TransformerInterface
{
    /**
     * @var ContenTypeTransformerInterface[]
     */
    private $transformers;

    /**
     * @param ContenTypeTransformerInterface[] $transformers
     */
    public function __construct(array $transformers)
    {
        $this->transformers = [];
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }
    }

    /**
     * @param ContenTypeTransformerInterface $transformer
     */
    private function addTransformer(ContenTypeTransformerInterface $transformer)
    {
        $this->transformers[$transformer->getContentType()] = $transformer;
    }

    /**
     * @return array
     */
    public function getContentTypes(): array
    {
        return array_keys($this->transformers);
    }

    /**
     * @param string $string
     * @param string $contentType
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function transform(string $string, string $contentType): array
    {
        if (isset($this->transformers[$contentType])) {
            return $this->transformers[$contentType]->transform($string);
        }

        throw new \InvalidArgumentException(sprintf('There is no transformer for content-type: %s', $contentType));
    }
}
