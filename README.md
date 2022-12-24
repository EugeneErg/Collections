# Типизированные коллекции #

## Иммутабельная типизированная коллекция классов
```php
class CustomCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = Custom::class,// Допустимое имя класса элемента
}
```
## Иммутабельная типизированная коллекция типов
```php
class CustomCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = 'is_int',// Метод, для валидации элемента
}
```
## Имеющиеся коллекции ##
```php
class MixedCollection implements CollectionInterface// Элементы любого типа


```