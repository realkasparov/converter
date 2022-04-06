<?php
declare(strict_types=1);

namespace App\Manager;

use App\Entity\Currency as Model;
use App\Exception\NotUpdateException;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CurrencyManager
{
    protected EntityManagerInterface $entityManager;
    protected CurrencyRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, CurrencyRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public function create(string $name, float $rate): Model
    {
        $object = new Model();
        $object
            ->setName($name)
            ->setRate($rate)
        ;

        return $object;
    }

    public function get(int $id): Model|null
    {
        return $this->repository->find($id);
    }

    public function getByName(string $name): Model|null
    {
        return $this->repository->findOneBy(['name' => $name]);
    }

    public function update(Model $object): Model
    {
        try {
            $this->entityManager->beginTransaction();
            $this->entityManager->persist($object);
            $this->entityManager->commit();
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw new NotUpdateException(\sprintf('Fail to update Currency with id: %s', $object->getId()), 0, $exception);
        }

        return $object;
    }
}