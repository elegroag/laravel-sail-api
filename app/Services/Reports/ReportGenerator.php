<?php

namespace App\Services\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportGenerator
{
    private ?Builder $query = null;
    private array $columns = [];
    private string $filename = 'reporte.csv';
    private int $chunkSize = 1000;
    private ?\Closure $filterCallback = null;

    public function __construct(private ReportFormatStrategy $strategy)
    {
    }

    /**
     * Set the source. Accepts a Builder, a Model instance, or a model class string.
     */
    public function for(Builder|Model|string $source): self
    {
        if ($source instanceof Builder) {
            $this->query = $source;
        } elseif ($source instanceof Model) {
            $this->query = $source->newQuery();
        } elseif (is_string($source) && class_exists($source)) {
            /** @var Model $m */
            $m = new $source();
            $this->query = $m->newQuery();
        } else {
            throw new \InvalidArgumentException('Fuente inválida para ReportGenerator');
        }
        return $this;
    }

    /**
     * Define columns as ["Header" => accessor]. Accessor can be dot-path or callable($row).
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function filename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function chunkSize(int $size): self
    {
        $this->chunkSize = max(1, $size);
        return $this;
    }

    /**
     * Optional filter callback to mutate the query before export.
     */
    public function filter(callable $callback): self
    {
        $this->filterCallback = $callback(...);
        return $this;
    }

    public function download(): StreamedResponse
    {
        if (!$this->query) {
            throw new \RuntimeException('No se definió la fuente (Builder/Model) para el reporte');
        }
        if (empty($this->columns)) {
            throw new \RuntimeException('Debe definir al menos una columna para el reporte');
        }

        if ($this->filterCallback) {
            ($this->filterCallback)($this->query);
        }

        $response = $this->strategy->stream($this->query, $this->columns, $this->chunkSize);
        $disposition = sprintf('attachment; filename="%s"', $this->filename);
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}
