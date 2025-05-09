<?php

namespace Modules\Base\Traits;

use Illuminate\Support\Str;

trait CamelCasing
{
    /**
     * Alter eloquent model behaviour so that model attributes can be accessed via camelCase, but more importantly,
     * attributes also get returned as camelCase fields.
     */
    public bool $enforceCamelCase = true;

    /**
     * Overloads the eloquent isGuardableColumn method to ensure that we are checking for the existence of
     * the snake_cased column name.
     *
     * @param  string  $key
     */
    protected function isGuardableColumn($key): bool
    {
        return parent::isGuardableColumn($this->getSnakeKey($key));
    }

    /**
     * Overloads the eloquent setAttribute method to ensure that fields accessed
     * in any case are converted to snake_case, which is the defacto standard
     * for field names in databases.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function setAttribute($key, $value): mixed
    {
        return parent::setAttribute($this->getSnakeKey($key), $value);
    }

    /**
     * Retrieve a given attribute but allow it to be accessed via alternative case methods (such as camelCase).
     *
     * @param  string  $key
     */
    public function getAttribute($key): mixed
    {
        // Check if the key is a cast or a custom accessor method exists
        if ($this->hasCast($key) || method_exists($this, $key)) {
            return parent::getAttribute($key);  // Use the parent method directly
        }

        // If the relationship is loaded, return the related value
        if ($this->relationLoaded($key)) {
            return $this->getRelationValue($key);
        }

        // Convert to snake_case and return the attribute using the parent method
        $snakeKey = $this->getSnakeKey($key);

        return parent::getAttribute($snakeKey);
    }

    /**
     * Return the attributes for the model, converting field casing if necessary.
     */
    public function attributesToArray(): array
    {
        return $this->toCamelCase(parent::attributesToArray());
    }

    /**
     * Get the model's relationships, converting field casing if necessary.
     */
    public function relationsToArray(): array
    {
        return $this->toCamelCase(parent::relationsToArray());
    }

    /**
     * Overloads eloquent's getHidden method to ensure that hidden fields declared
     * in camelCase are actually hidden and not exposed when models are turned
     * into arrays.
     */
    public function getHidden(): array
    {
        return array_map(Str::class.'::snake', $this->hidden);
    }

    /**
     * Overloads the eloquent getDates method to ensure that date field declarations
     * can be made in camelCase but mapped to/from DB in snake_case.
     */
    public function getDates(): array
    {
        $dates = parent::getDates();

        return array_map(Str::class.'::snake', $dates);
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCaseModel.
     */
    public function toCamelCase(mixed $attributes): array
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $key = $this->getTrueKey($key);
            $convertedAttributes[$key] = $value;
        }

        return $convertedAttributes;
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCaseModel.
     */
    public function toSnakeCase($attributes): array
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $convertedAttributes[$this->getSnakeKey($key)] = $value;
        }

        return $convertedAttributes;
    }

    /**
     * Retrieves the true key name for a key.
     */
    public function getTrueKey($key): string
    {
        // If the key is a pivot key, leave it alone - this is required internal behaviour
        // of Eloquent for dealing with many:many relationships.
        if ($this->isCamelCase() && ! str_contains($key, 'pivot_')) {
            $key = Str::camel($key);
        }

        return $key;
    }

    /**
     * Determines whether the model (or its parent) requires camelcasing. This is required
     * for pivot models whereby they actually depend on their parents for this feature.
     */
    public function isCamelCase(): bool
    {
        return $this->enforceCamelCase or (isset($this->parent) && method_exists($this->parent, 'isCamelCase') && $this->parent->isCamelCase());
    }

    /**
     * If the field names need to be converted so that they can be accessed by camelCase, then we can do that here.
     */
    protected function getSnakeKey($key): string
    {
        return Str::snake($key);
    }

    /**
     * Because we are changing the case of keys and want to use camelCase throughout the application, whenever
     * we do isset checks we need to ensure that we check using snake_case.
     *
     * @return mixed
     */
    public function __isset($key)
    {
        return parent::__isset($key) || parent::__isset($this->getSnakeKey($key));
    }

    /**
     * Because we are changing the case of keys and want to use camelCase throughout the application, whenever
     * we do unset variables we need to ensure that we unset using snake_case.
     *
     * @return void
     */
    public function __unset($key)
    {
        return parent::__unset($this->getSnakeKey($key));
    }
}
