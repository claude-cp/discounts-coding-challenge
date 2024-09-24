<?php

declare(strict_types=1);

namespace App\Tests\AppCommon\ParamConverter;

use App\AppCommon\Exception\ViolationException;
use App\AppCommon\ParamConverter\InputModelParamConverter;
use App\Tests\AppCommon\ParamConverter\Model\DemoModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InputModelParamConverterTest extends TestCase
{
    public function testApply(): void
    {
        $content = '{"name": "demo name"}';

        $request = Request::create('', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);

        /** @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')
            ->with($content, DemoModel::class, 'json', [])
            ->willReturn((new DemoModel())->setName('demo name'));

        $converter = new InputModelParamConverter(
            $serializer,
            $this->createMock(ValidatorInterface::class)
        );

        $configuration = new ParamConverter(
            [
                'name' => 'demo',
                'class' => DemoModel::class,
            ]
        );

        $converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('demo'));

        /** @var DemoModel $model */
        $model = $request->attributes->get('demo');
        self::assertInstanceOf(DemoModel::class, $model);
        self::assertEquals('demo name', $model->getName());
    }

    public function testApplyOptionalParamWithNullContent(): void
    {
        $content = null;

        $request = Request::create('', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);

        /** @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects(self::never())
            ->method('deserialize')
            ->with($content, DemoModel::class, 'json', [])
            ->willReturn((new DemoModel())->setName('demo name'));

        $converter = new InputModelParamConverter(
            $serializer,
            $this->createMock(ValidatorInterface::class)
        );

        $configuration = new ParamConverter(
            [
                'name' => 'demo',
                'class' => DemoModel::class,
            ]
        );
        $configuration->setIsOptional(true);

        $converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('demo'));

        /** @var DemoModel|null $model */
        $model = $request->attributes->get('demo');
        self::assertNull($model);
    }

    public function testApplyAndValidate(): void
    {
        $content = '{"name": "demo name"}';

        $request = Request::create('', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);

        $inputModel = (new DemoModel())->setName('demo name');

        /** @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')
            ->with($content, DemoModel::class, 'json', [])
            ->willReturn($inputModel);

        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->with($inputModel, null, ['Group1', 'Group2'])
            ->willReturn(new ConstraintViolationList());

        $converter = new InputModelParamConverter(
            $serializer,
            $validator
        );

        $configuration = new ParamConverter(
            [
                'name' => 'demo',
                'class' => DemoModel::class,
                'options' => [
                    'validate' => true,
                    'validation_groups' => ['Group1', 'Group2'],
                ],
            ]
        );

        $converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('demo'));

        /** @var DemoModel $model */
        $model = $request->attributes->get('demo');
        self::assertInstanceOf(DemoModel::class, $model);
        self::assertEquals('demo name', $model->getName());
    }

    public function testApplyAndValidateOptionalWithNullContent(): void
    {
        $content = null;

        $request = Request::create('', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);

        $inputModel = (new DemoModel())->setName('demo name');

        /** @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects(self::never())
            ->method('deserialize')
            ->with($content, DemoModel::class, 'json', []);

        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects(self::never())
            ->method('validate')
            ->with($inputModel, null, ['Group1', 'Group2']);

        $converter = new InputModelParamConverter(
            $serializer,
            $validator
        );

        $configuration = new ParamConverter(
            [
                'name' => 'demo',
                'class' => DemoModel::class,
                'options' => [
                    'validate' => true,
                    'validation_groups' => ['Group1', 'Group2'],
                ],
            ]
        );
        $configuration->setIsOptional(true);

        $converter->apply($request, $configuration);

        self::assertTrue($request->attributes->has('demo'));

        /** @var DemoModel|null $model */
        $model = $request->attributes->get('demo');
        self::assertNull($model);
    }

    public function testFailsValidation(): void
    {
        $this->expectException(ViolationException::class);

        $content = '{"name": "demo name"}';

        $request = Request::create('', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);

        $inputModel = (new DemoModel())->setName('demo name');

        /** @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')
            ->with($content, DemoModel::class, 'json', [])
            ->willReturn($inputModel);

        /** @var ConstraintViolationList&MockObject $constraintViolations */
        $constraintViolations = $this->createMock(ConstraintViolationList::class);
        $constraintViolations
            ->method('count')
            ->willReturn(1);

        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->with($inputModel, null, ['Default'])
            ->willReturn($constraintViolations);

        $converter = new InputModelParamConverter(
            $serializer,
            $validator
        );

        $configuration = new ParamConverter(
            [
                'name' => 'demo',
                'class' => DemoModel::class,
                'options' => [
                    'validate' => true,
                ],
            ]
        );

        $converter->apply($request, $configuration);
    }

    /**
     * @dataProvider configurationDataProvider
     */
    public function testSupports(ParamConverter $configuration, bool $expected): void
    {
        $converter = new InputModelParamConverter(
            $this->createMock(SerializerInterface::class),
            $this->createMock(ValidatorInterface::class)
        );

        self::assertEquals($expected, $converter->supports($configuration));
    }

    public function configurationDataProvider(): \Generator
    {
        yield [new ParamConverter([]), false];
        yield [new ParamConverter(['class' => DemoModel::class]), true];
    }
}
