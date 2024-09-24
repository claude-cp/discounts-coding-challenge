<?php

declare(strict_types=1);

namespace App\Tests\AppCommon\ParamConverter\Model;

use App\AppCommon\Model\InputModelInterface;

class DemoModel implements InputModelInterface
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
