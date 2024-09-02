<?php

namespace App\Http\Dto;

readonly class RolesDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $active,
        public string $created_at
    ) {}

    public static function contentList($list): array
    {
        return array_map(function ($item) {
            return new RolesDto($item['id'], $item['name'], $item['active'], $item['created_at']);
        }, $list);
    }

    public static function content($item): RolesDto
    {
        return new RolesDto($item['id'], $item['name'], $item['active'], $item['created_at']);
    }
}
