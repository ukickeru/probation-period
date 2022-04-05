<?php

namespace Mygento\AccessControlBundle\ACL\Domain\Service;

class ACEsCollection implements \Countable
{
    private array $ACEs = [];

    public function __construct(array $ACEs = [])
    {
        foreach ($ACEs as $ACE) {
            if (!is_array($ACE) || 2 !== count($ACE) || empty($ACE[0]) || empty($ACE[1]) || in_array($ACE, $this->ACEs)) {
                throw new \DomainException('ACE must be presented as unique non-null pair [User id, Resource id]!');
            }

            $this->ACEs[] = $ACE;
        }
    }

    public function getACEs(): array
    {
        return $this->ACEs;
    }

    public function addACE(array $ACE)
    {
        if (in_array($ACE, $this->ACEs)) {
            throw new \DomainException('ACE must be presented as unique non-null pair [User id, Resource id]!');
        }

        $this->ACEs[] = $ACE;
    }

    /**
     * @return string if format like "(userId, resourceId), ..., (userId, resourceId)"
     */
    public function __toString(): string
    {
        $valuesStr = '';
        $ACEsCount = count($this->ACEs);

        for ($i = 0; $i < $ACEsCount; ++$i) {
            $valuesStr .= '('.implode(',', $this->ACEs[$i]).')';
            if ($i < $ACEsCount - 1) {
                $valuesStr .= ',';
            }
        }

        return $valuesStr;
    }

    public function count(): int
    {
        return count($this->ACEs);
    }
}
