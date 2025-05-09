<?php

namespace App\Filament\FilamentCustom\Tables\Columns;

use App\Filament\FilamentCustom\Tables\Columns\Traits\ColumnColor;
use Closure;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class RelationViewColumn extends TextColumn
{
    use ColumnColor;

    protected FontWeight|string|Closure|null $weight = FontWeight::Bold;

    protected string|array|bool|Closure|null $color = 'primary';

    protected bool|Closure $canWrap = true;

    protected string $resource = '';

    protected ?string $relation = null;

    protected string|Closure|null $url = null;

    protected string|Htmlable|Closure|null $placeholder = '-';

    public function relation(?string $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    protected function getRelatedRecord(): ?Model
    {
        $relation = $this->getRelation();

        return object_get($this->getRecord(), $relation);
    }

    public function getUrl(): ?string
    {
        if (! class_exists($this->getResource())) {
            return null;
        }

        $resource = new ($this->getResource());

        if (! $this->getRecord() || ! ($resource instanceof Resource)) {
            return null;
        }

        $relationRecord = $this->getRelatedRecord();

        if (! $relationRecord) {
            return null;
        }

        $page = 'edit';

        if ($resource::hasPage('view')) {
            $page = 'view';
        }

        return $resource::getUrl($page, [
            'record' => $relationRecord,
        ]);
    }
}
