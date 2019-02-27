<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Entities;

class Entity
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return \SebastiaanLuca\Module\Entities\Module
     */
    private function fill(array $attributes) : self
    {
        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }

        return $this;
    }
}
