<?php

declare(strict_types=1);

namespace EugeneErg\Collections\Enums;

enum SortTypeEnum: int
{
    case String = SORT_STRING;
    case Numeric = SORT_NUMERIC;
    case LocaleString = SORT_LOCALE_STRING;
    case Natural = SORT_NATURAL;
}
