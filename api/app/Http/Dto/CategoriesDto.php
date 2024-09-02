<?php

namespace App\Http\Dto;

readonly class CategoriesDto
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
            return self::content($item);
        }, $list);
    }

    public static function content($item): CategoriesDto
    {
        return new CategoriesDto($item['id'], $item['name'], $item['active'], $item['created_at']);
    }
}
