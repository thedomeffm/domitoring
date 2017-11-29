<?php

namespace AppBundle\Repository;

/**
 * HistoryServerPingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoryServerPingRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBetweenDatetimeAndToday(\DateTime $from)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')
            ->from('AppBundle:HistoryServerPing', 'e')
            ->where('e.pingDatetime > :from')
            ->setParameter('from', $from);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getLastAccident()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')
            ->from('AppBundle:HistoryServerPing', 'e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults('1');

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}