<?php 

namespace Richpolis\ComentariosBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Richpolis\ComentariosBundle\Entity\Comentario;

class ComentarioToNumberTransformer implements DataTransformerInterface
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
    public function transform($comentario)
    {
        if (null === $comentario) {
            return "";
        }

        return $comentario->getId();
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

        $comentario = $this->om
            ->getRepository('ComentariosBundle:Comentario')
            ->find($number);
        ;

        if (null === $comentario) {
            throw new TransformationFailedException(sprintf(
                'An Comentario with id "%s" does not exist!',
                $number
            ));
        }

        return $comentario;
    }
}