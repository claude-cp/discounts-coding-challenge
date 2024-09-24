<?php

declare(strict_types=1);

namespace App\AppCommon\ParamConverter;

use App\AppCommon\Exception\ViolationException;
use App\AppCommon\Model\InputModelInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Converts request payload to input model.
 *
 * Supported option parameters: see method "getOptions" in this class.
 */
final class InputModelParamConverter implements ParamConverterInterface
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        $object = null;
        if ($request->getContent()) {
            $object = $this->serializer->deserialize(
                $request->getContent(),
                $class,
                $request->getContentTypeFormat(),
                $options['serializer_context']
            );
        } elseif (!$configuration->isOptional()) {
            throw new BadRequestHttpException('Missing request body.');
        }

        if ($object && $options['validate']) {
            $violations = $this->validator->validate($object, null, $options['validation_groups']);

            if ($violations->count() > 0) {
                throw new ViolationException($violations);
            }
        }

        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        if (empty($configuration->getClass())) {
            return false;
        }

        return \in_array(InputModelInterface::class, class_implements($configuration->getClass()), true);
    }

    private function getOptions(ParamConverter $configuration): array
    {
        return array_replace(
            [
                'validate' => false,
                'validation_groups' => ['Default'],
                'serializer_context' => [],
            ],
            $configuration->getOptions()
        );
    }
}
