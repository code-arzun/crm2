<?php
namespace App\Enums;

enum CustomerSegment: string {
    case NEW = 'new';
    case ACTIVE = 'active';
    case LOYAL = 'loyal';
    case DORMANT = 'dormant';

    public function label(): string {
        return match($this) {
            self::NEW => 'Baru',
            self::ACTIVE => 'Aktif',
            self::LOYAL => 'Loyal',
            self::DORMANT => 'Dormant',
        };
    }
}