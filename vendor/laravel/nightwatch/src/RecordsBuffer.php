<?php

namespace Laravel\Nightwatch;

use Countable;

use function array_shift;
use function count;
use function json_encode;

/**
 * @internal
 */
class RecordsBuffer implements Countable
{
    /**
     * @var list<array<mixed>>
     */
    private array $records = [];

    public bool $full = false;

    public function __construct(private int $length)
    {
        //
    }

    /**
     * @param  array<string, mixed>  $record
     */
    public function write(array $record): void
    {
        if ($this->full) {
            array_shift($this->records);
        }

        $this->records[] = $record;

        $this->full = $this->count() >= $this->length;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function pull(string $tokenHash): Payload
    {
        if ($this->records === []) {
            return Payload::json('[]', $tokenHash);
        }

        $records = json_encode($this->records, flags: JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);

        $this->flush();

        return Payload::json($records, $tokenHash);
    }

    public function flush(): void
    {
        $this->records = [];
        $this->full = false;
    }
}
