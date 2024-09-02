<?php

namespace App\Http\Dto;

readonly class ProductsDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $price,
        public string $expiration_date,
        public string $image,
        public bool $active,
        public CategoriesDto $category,
        public string | null $created_at
    ) {}

    public static function contentList($list): array
    {
        return array_map(function ($item) {
            return self::content($item);
        }, $list);
    }

    public static function content($item): ProductsDto
    {
        $category = CategoriesDto::content($item['category']);
        return new ProductsDto(
            $item['id'],
            $item['name'],
            (string) $item['description'],
            $item['price'],
            $item['expiration_date'],
            $item['image'],
            $item['active'],
            $category,
            $item['created_at']
        );
    }
}
