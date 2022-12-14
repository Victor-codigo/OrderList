<?php

declare(strict_types=1);

namespace Common\Adapter\Http\ArgumentResolver;

use Common\Adapter\Http\Dto\RequestDtoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class ArgumentResolver implements ArgumentValueResolverInterface
{
    private RequestValidation $requestValidation;

    public function __construct(RequestValidation $requestValidation)
    {
        $this->requestValidation = $requestValidation;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (null === $argument->getType()) {
            return false;
        }

        $requestReflection = new \ReflectionClass($argument->getType());

        return $requestReflection->implementsInterface(RequestDtoInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $this->requestValidation->__invoke($request);
        $requestDto = $argument->getType();

        yield new $requestDto($request);
    }
}
