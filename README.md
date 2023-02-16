# Типизированные коллекции #

## Объявление типизированной коллекции
```php
class CustomCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Custom::class;// Допустимое имя класса элемента
}
```
## Создание иммутабельной коллекции
```php
$collection = new CustomCollection([new Custom()], true/*иммутабельная*/);

unset($collection[0]);//Будет выброшено исключение
```
## Список предопределенных коллекций:
```php
namespace EugeneErg\Collections;

use MixedCollection;// Любые типы
use ObjectCollection;// Объекты
use ScalarCollection;// Скалярные типы
use NumberCollection;// Числовые типы
use FloatCollection;// Числа с плавающей точкой
use IntegerCollection;// Целые числа
use StringCollection;// Строки
use SortCollection;// Классы наследующие Sort
use BooleanCollection;// Булевы 
```

## Установка

Добавьте в **composer.json**
```json
{
  ...
  "repositories": [
    ...
    {
      "type": "git",
      "url": "git@github.com:EugeneErg/Collections.git"
    }
  ]
}
```

Затем выполните
```
composer require EugeneErg/Collections
```