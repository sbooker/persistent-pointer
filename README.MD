[Read in English](README.EN.MD)

# Persistent Pointer (`sbooker/persistent-pointer`)

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

Простая библиотека для создания и управления именованными, персистентными "указателями" — счетчиками, которые могут только увеличиваться.

## Назначение библиотеки

При создании фоновых обработчиков (воркеров, консьюмеров) часто возникает задача: **как надежно сохранить позицию, на которой остановился обработчик в прошлый раз?**

Эта библиотека решает данную задачу, предоставляя `Pointer` — простой объект, который хранит именованный целочисленный счетчик в базе данных.

## Ключевые особенности

*   **Персистентность:** Состояние указателя сохраняется в хранилище (например, в базе данных).
*   **Безопасность при конкурентном доступе:** Метод `getLocked()` гарантирует, что только один процесс может работать с указателем в один момент времени.
*   **Монотонное возрастание:** Значение указателя можно только увеличить с помощью метода `increaseTo()`. Это защищает от случайного "отката" прогресса.
*   **Абстракция над хранилищем:** Библиотека не зависит от конкретной ORM благодаря интерфейсу `PointerStorage`.

## Установка

```bash
composer require sbooker/persistent-pointer
```

## Быстрый старт

### Шаг 1: Реализуйте `PointerStorage`

Вам нужно создать "мост" к вашей ORM. Этот адаптер будет использоваться "под капотом" `TransactionManager`.

```php
// src/Infrastructure/Persistence/PointerStorage.php

use Sbooker\PersistentPointer\Pointer;
use Sbooker\PersistentPointer\PointerStorage;
use Sbooker\TransactionManager\TransactionManager; 

final class DoctrinePointerStorage implements PointerStorage
{
    private TransactionManager $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function add(Pointer $pointer): void
    {
        // Делегируем всю работу TransactionManager
        $this->transactionManager->persist($pointer);
    }

    public function getAndLock(string $name): ?Pointer
    {
        // Делегируем всю работу TransactionManager
        return $this->transactionManager->getLocked(Pointer::class, $name);
    }
}
```

### Шаг 2: Соберите `Repository`

Соберите репозиторий, передав в него вашу реализацию `PointerStorage`.

```php
// bootstrap.php или ваш DI-контейнер

/** @var TransactionManager $transactionManager */
$pointerStorage = new PointerStorage($transactionManager);
$pointerRepository = new Sbooker\PersistentPointer\Repository($pointerStorage);
```

### Шаг 3: Используйте в фоновом обработчике

`Pointer` идеально раскрывает себя внутри транзакции, управляемой `sbooker/transaction-manager`. Это гарантирует, что и обработка данных, и обновление указателя произойдут атомарно.

```php
// src/Worker/EventProcessor.php
final class EventProcessor
{
    private const POINTER_NAME = 'event_processor';

    private Repository $pointerRepository;
    private TransactionManager $transactionManager;
    private EventRepository $eventRepository; // Репозиторий для ваших событий

    // ... constructor ...

    public function processBatch(): void
    {
        $this->transactionManager->transactional(function (): void {
            // 1. Получаем указатель с блокировкой.
            // Этот вызов будет использовать getLocked() из TransactionManager,
            // обеспечивая блокировку внутри общей транзакции.
            $pointer = $this->pointerRepository->getLocked(self::POINTER_NAME);
            $lastPosition = $pointer->getValue();

            // 2. Получаем новую порцию данных
            $events = $this->eventRepository->findSince($lastPosition);

            if (empty($events)) {
                return;
            }

            // 3. Обрабатываем данные...
            foreach ($events as $event) {
                // ... do some work ...
                $lastPosition = $event->getPosition();
            }

            // 4. Передвигаем указатель на новую позицию
            $pointer->increaseTo($lastPosition);

            // TransactionManager атомарно сохранит и новое значение указателя,
            // и любые другие изменения, сделанные в этой транзакции.
        });
    }
}
```

## License
See [LICENSE][license] file.

[badge-release]: https://img.shields.io/packagist/v/sbooker/persistent-pointer.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/sbooker/persistent-pointer.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/sbooker/persistent-pointer.svg?style=flat-square

[release]: https://packagist.org/packages/sbooker/persistent-pointer
[license]: https://github.com/sbooker/persistent-pointer/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/sbooker/persistent-pointer

[composer]: https://getcomposer.org