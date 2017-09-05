<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Badge;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * BadgeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BadgeRepository extends EntityRepository
{
    /**
     * @param $actionName
     * @param $actionCount
     * @param $userId
     * @return Badge
     */
    public function findBadgeAndNotUsedForUser($actionName, $actionCount, $userId)
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->where('b.actionName = :actionName')
            ->andWhere('b.actionCount = :actionCount')
            ->andWhere('u.user = :userId OR u.user IS NULL')
            ->leftJoin('b.unlockBadge', 'u', Join::WITH, 'u.user = :userId')
            ->setParameter('actionName', $actionName)
            ->setParameter('actionCount', $actionCount)
            ->setParameter('userId', $userId)
            ->select('b, u');

        return $qb->getQuery()->getSingleResult();
    }

    public function findBadgeFor($userId)
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->join('b.unlockBadge', 'u')
            ->where('u.user = :userId')
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }
}
