<?php

namespace App\Indexation;

use App\Entity\IndexSearchInterface;

interface IndexationInterface
{
    public function create(IndexSearchInterface $index): bool;

    public function update(IndexSearchInterface $index): bool;

    public function delete(IndexSearchInterface $index): bool;

    /** @return array */
    public function search(array $options = []);
}