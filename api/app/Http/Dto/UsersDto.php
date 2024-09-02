<?php

namespace App\Http\Dto;

readonly class UsersDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public RolesDto $role,
        public bool $active,
        public string $created_at
    ) {}

    public static function contentList($list): array
    {
        return array_map(function ($item) {
            $role = RolesDto::content($item['role']);
            return new UsersDto($item['id'], $item['name'], $item['email'], $role, $item['active'], $item['created_at']);
        }, $list);
    }

    public static function content($item): array
    {
        $role = RolesDto::content($item['role']);
        return (array) new UsersDto($item['id'], $item['name'], $item['email'], $role, $item['active'], $item['created_at']);
    }
}
