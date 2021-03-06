<?php

declare(strict_types=1);

namespace PhpSerializers\Benchmarks\Bench;

use PhpSerializers\Benchmarks\AbstractBench;

/**
 * @author scyzoryck <scyzoryck@gmail.com>
 */
class JsonSerializableBenchmark extends AbstractBench
{
    public function initSerializer(): void
    {

    }

    public function serialize($data): void
    {
        json_encode(
            $data
        );
    }

    public function getPackageName(): string
    {
        return 'php/json-serializable';
    }

    public function getNote(): string
    {
        return <<<'NOTE'
Serialize object graphs using the native JsonSerializable interface
NOTE;
    }
}
