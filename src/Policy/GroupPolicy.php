<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class GroupPolicy implements PolicyInterface
{
    public const ATTRIBUTE_GROUPS = 'groups';

    public const GROUP_DEFAULT = 'default';

    /**
     * @var array<int, string>
     */
    private $groups;

    /**
     * @param array<int, string> $groups
     */
    public function __construct(array $groups = [self::GROUP_DEFAULT])
    {
        $this->groups = $groups;
    }

    /**
     * @deprecated
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        return $this->isCompliantIncludingPath($object, $context, '');
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool
    {
        if ([] === $this->groups) {
            return true;
        }

        $contextGroups = $context->getAttribute(self::ATTRIBUTE_GROUPS, [self::GROUP_DEFAULT]);

        foreach ($this->groups as $group) {
            if (in_array($group, $contextGroups, true)) {
                return true;
            }
        }

        return false;
    }
}
