<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @todo remove as soon isCompliantIncludingPath() is part of the policy interface
 */
trait PolicyIncludingPathTrait
{
    private function getCompliantPolicyIncludingPath(bool $isCompliant): PolicyInterface {
        return new class($isCompliant) implements PolicyInterface {
            private $isPolicyCompliant;

            public function __construct($isPolicyCompliant)
            {
                $this->isPolicyCompliant = $isPolicyCompliant;
            }

            public function isCompliant(DenormalizerContextInterface $context, object $object): bool
            {
                return $this->isPolicyCompliant;
            }

            public function isCompliantIncludingPath(
                object $object,
                DenormalizerContextInterface $context,
                string $path
            ): bool {
                return $this->isPolicyCompliant;
            }
        };
    }
}
