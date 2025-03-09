<?php

namespace Tenweb_Builder\Apps\CoPilot\Interfaces;

interface BaseRepositoryInterface
{
    public function get($args = []);
    public function find($id);
    public function delete($id);
    public function publish($id);
    public function unpublish($id);
    public function create(array $data);
    public function clone(int $id);
    public function getIdByTitle(string $title);
}