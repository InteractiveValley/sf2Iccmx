<?php 

namespace Richpolis\PublicacionesBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\PublicacionesBundle\Entity\Publicacion;

class PublicacionToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($publicacion)
    {
        if (null === $publicacion) {
            return "";
        }

        return $publicacion->getId();
    }

    /**
     * Transforms a string (number) to an object (comentario).
     *
     * @param  string $number
     *
     * @return Comentario|null
     *
     * @throws TransformationFailedException if object (comentario) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $publicacion = $this->om
            ->getRepository('PublicacionesBundle:Publicacion')
            ->find($number);
        ;

        if (null === $publicacion) {
            throw new TransformationFailedException(sprintf(
                'An Publicacion with id "%s" does not exist!',
                $number
            ));
        }

        return $publicacion;
    }
}