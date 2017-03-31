<?php

namespace GoPague\Support;

interface Arrayable
{
    /**
     * Returns the array representation of the resource
     *
     * @return string
     */
    public function toArray() : array;
}
