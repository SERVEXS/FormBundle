<?php

declare(strict_types=1);

namespace Gregwar\FormBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Data transformation class
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    private string $class;

    private ?string $property = null;

    /**
     * @var \Closure|QueryBuilder|null
     */
    private $queryBuilder;

    private bool $multiple;

    private UnitOfWork $unitOfWork;

    /**
     * @param \Closure|QueryBuilder|null $queryBuilder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ?string $class,
        ?string $property = null,
        $queryBuilder = null,
        bool $multiple = false
    ) {
        if (!(null === $queryBuilder || $queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if (null == $class) {
            throw new UnexpectedTypeException($class, 'string');
        }

        $this->entityManager = $entityManager;
        $this->unitOfWork = $this->entityManager->getUnitOfWork();
        $this->class = $class;
        $this->queryBuilder = $queryBuilder;
        $this->multiple = $multiple;

        if ($property) {
            $this->property = $property;
        }
    }

    public function transform($data)
    {
        if (null === $data) {
            return null;
        }

        if (!$this->multiple) {
            return $this->transformSingleEntity($data);
        }

        $return = [];

        foreach ($data as $element) {
            $return[] = $this->transformSingleEntity($element);
        }

        return implode(', ', $return);
    }

    protected function splitData($data)
    {
        return is_array($data) ? $data : explode(',', $data);
    }

    /**
     * @return false|mixed|null
     *
     * @throws EntityNotFoundException
     */
    protected function transformSingleEntity($data)
    {
        if (!$this->unitOfWork->isInIdentityMap($data)) {
            throw new TransformationFailedException('Entities passed to the choice field must be managed');
        }

        if ($this->property) {
            return (new PropertyAccessor())->getValue($data, $this->property);
        }

        return current($this->unitOfWork->getEntityIdentifier($data));
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return null;
        }

        if (!$this->multiple) {
            return $this->reverseTransformSingleEntity($data);
        }

        $return = [];

        foreach ($this->splitData($data) as $element) {
            $return[] = $this->reverseTransformSingleEntity($element);
        }

        return $return;
    }

    protected function reverseTransformSingleEntity($data)
    {
        $em = $this->entityManager;
        $class = $this->class;
        $repository = $em->getRepository($class);

        if ($qb = $this->queryBuilder) {
            if ($qb instanceof \Closure) {
                $qb = $qb($repository, $data);
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                $result = null;
            }
        } else {
            if ($this->property) {
                $result = $repository->findOneBy([$this->property => $data]);
            } else {
                $result = $repository->find($data);
            }
        }

        if (!$result) {
            throw new TransformationFailedException('Can not find entity');
        }

        return $result;
    }
}
