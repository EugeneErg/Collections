<?php

declare(strict_types=1);

namespace EugeneErg\Collections\Enums;

enum SortTypeEnum: int
{
    case String = 2;//SORT_STRING;
    case Numeric = 1;//SORT_NUMERIC;
    case LocaleString = 5;//SORT_LOCALE_STRING;
    case Natural = 6;//SORT_NATURAL;
}
