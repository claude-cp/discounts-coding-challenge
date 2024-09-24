<?php

declare(strict_types=1);

namespace App\AppCommon\ParamConverter;

use App\AppCommon\Exception\ViolationException;
use App\AppCommon\Model\RequestQueryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Converts request payload to input model. Supported option parameters:
 * validate (bool) - validates input model. Default false.
 * validation_groups (array) - validation groups to use. Default ['Default']
 * serializer_context (array) - serializer context passed to Serializer deserialize function.
 *     Default ['disable_type_enforcement' => true].
 */
final class RequestQueryModelParamConverter implements ParamConverterInterface
{
    /**
     * @var GetSetMethodNormalizer
     */
    private $denormalizer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * RequestQueryModelParamConverter constructor.
     */
    public function __construct(GetSetMethodNormalizer $denormalizer, ValidatorInterface $validator)
    {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $options = $this->getOptions($configuration);

        try {
            $object = $this->denormalizer->denormalize(
                $request->query->all(),
                $configuration->getClass(),
                null,
                $options['serializer_context']
            );
        } catch (NotNormalizableValueException $exception) {
            throw $this->createNormalizationException($exception);
        }

        if ($options['validate']) {
            $violations = $this->validator->validate($object, null, $options['validation_groups']);

            if ($violations->count() > 0) {
                throw new ViolationException($violations);
            }
        }

        $request->attributes->set($configuration->getName(), $object);

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

        return \in_array(RequestQueryInterface::class, class_implements($configuration->getClass()), true);
    }

    private function getOptions(ParamConverter $configuration): array
    {
        return array_replace(
            [
                'validate' => false,
                'validation_groups' => ['Default'],
                'serializer_context' => [
                    GetSetMethodNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                ],
            ],
            $configuration->getOptions()
        );
    }

    private function createNormalizationException(NotNormalizableValueException $exception): \LogicException
    {
        preg_match_all('/(["\'])(?:(?=())\2.)*?\1/', $exception->getMessage(), $matches);

        $matches = current($matches);

        /*Indexes values:
         * 0 -> property name
         * 2 -> required type
         * 3 -> given type
        */
        $message = sprintf(
            'The type of the %s must be %s instead given %s',
            $matches[0],
            $matches[2],
            $matches[3]
        );

        return new \LogicException($message, $exception->getCode(), $exception);
    }
}
