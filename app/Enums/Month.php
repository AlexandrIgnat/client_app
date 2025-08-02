<?php

namespace App\Enums;

enum Month: int
{
    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    public function label(): string
    {
        return match($this) {
            self::JANUARY => 'Январь',
            self::FEBRUARY => 'Февраль',
            self::MARCH => 'Март',
            self::APRIL => 'Апрель',
            self::MAY => 'Май',
            self::JUNE => 'Июнь',
            self::JULY => 'Июль',
            self::AUGUST => 'Август',
            self::SEPTEMBER => 'Сентябрь',
            self::OCTOBER => 'Октябрь',
            self::NOVEMBER => 'Ноябрь',
            self::DECEMBER => 'Декабрь',
        };
    }
}
