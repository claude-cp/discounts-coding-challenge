<?php

declare(strict_types=1);

namespace App\Tests\AppCommon\ParamConverter;

use App\AppCommon\Exception\ViolationException;
use App\AppCommon\Model\RequestQueryInterface;
use App\AppCommon\ParamConverter\RequestQueryModelParamConverter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestQueryModelParamConverterTest extends TestCase
{
    public function testApply(): void
    {
        $query = $this->createMock(RequestQueryInterface::class);

        $normalizer = $this->createMock(GetSetMethodNormalizer::class);
        $normalizer->expects($this->once())
            ->method('denormalize')
            ->with([], 'DemoClass')
            ->willReturn($query);

        $validator = $this->createMock(ValidatorInterface::class);
        $converter = new RequestQueryModelParamConverter($normalizer, $validator);

        $request = new Request();
        $configuration = new ParamConverter(['class' => 'DemoClass', 'name' => 'query']);

        $result = $converter->apply($request, $configuration);

        $this->assertTrue($request->attributes->has('query'));
        $this->assertTrue($result);
        $this->assertEquals($query, $request->attributes->get('query'));
    }

    public function testApplyAndValidate(): void
    {
        $query = $this->createMock(RequestQueryInterface::class);

        $normalizer = $this->createMock(GetSetMethodNormalizer::class);
        $normalizer->expects($this->once())
            ->method('denormalize')
            ->with([], 'DemoClass')
            ->willReturn($query);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($query, null, ['Group1', 'Group2'])
            ->willReturn(new ConstraintViolationList());

        $converter = new RequestQueryModelParamConverter($normalizer, $validator);

        $request = new Request();
        $configuration = new ParamConverter(
            [
                'class' => 'DemoClass',
                'name' => 'query',
                'options' => [
                    'validate' => true,
                    'validation_groups' => ['Group1', 'Group2'],
                ],
            ]
        );

        $result = $converter->apply($request, $configuration);

        $this->assertTrue($request->attributes->has('query'));
        $this->assertTrue($result);
        $this->assertEquals($query, $request->attributes->get('query'));
    }

    public function testFailsValidation(): void
    {
        $this->expectException(ViolationException::class);

        $query = $this->createMock(RequestQueryInterface::class);

        $normalizer = $this->createMock(GetSetMethodNormalizer::class);
        $normalizer->expects($this->once())
            ->method('denormalize')
            ->with([], 'DemoClass')
            ->willReturn($query);

        /** @var ConstraintViolationList&MockObject $constraintViolations */
        $constraintViolations = $this->createMock(ConstraintViolationList::class);
        $constraintViolations
            ->method('count')
            ->willReturn(1);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($query, null, ['Default'])
            ->willReturn($constraintViolations);

        $converter = new RequestQueryModelParamConverter($normalizer, $validator);

        $request = new Request();
        $configuration = new ParamConverter(
            [
                'class' => 'DemoClass',
                'name' => 'query',
                'options' => [
                    'validate' => true,
                ],
            ]
        );

        $converter->apply($request, $configuration);
    }

    /**
     * @dataProvider supportedParamDataProvider
     */
    public function testSupports(ParamConverter $configuration, bool $expected): void
    {
        $normalizer = $this->createMock(GetSetMethodNormalizer::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $converter = new RequestQueryModelParamConverter($normalizer, $validator);
        $result = $converter->supports($configuration);

        $this->assertEquals($expected, $result);
    }

    public function supportedParamDataProvider(): \Generator
    {
        yield [new ParamConverter(['class' => \stdClass::class, 'name' => 'query']), false];

        $mock = $this->createMock(RequestQueryInterface::class);
        yield [new ParamConverter(['class' => \get_class($mock), 'name' => 'query']), true];
    }
}
