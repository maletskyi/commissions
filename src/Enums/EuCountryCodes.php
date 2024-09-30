<?php

declare(strict_types=1);

namespace App\Enums;

enum EuCountryCodes
{
    case AT;

    case BE;

    case BG;

    case CY;

    case CZ;

    case DE;

    case DK;

    case EE;

    case ES;

    case FI;

    case FR;

    case GR;

    case HR;

    case HU;

    case IE;

    case IT;

    case LT;

    case LU;

    case LV;

    case MT;

    case NL;

    case PO;

    case PT;

    case RO;

    case SE;

    case SI;

    case SK;

    public static function getByName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
