# Task Manager API

REST API для управления задачами.

## Технологии

* PHP 8.3
* Laravel 12
* PostgreSQL 16
* Docker Compose
* Laravel Sanctum
* PHPUnit

## Возможности

* Создание, получение, обновление и удаление задач.
* Фильтрация задач по статусу.
* Пагинация списка задач с настройкой размера страницы.
* Валидация входных данных.
* Bearer token-аутентификация.
* Feature-тесты API.

## Требования

* Docker
* Docker Compose

## Запуск проекта

### 1. Подготовить переменные окружения

Создать `.env` на основе `.env.example`:

```bash
cp .env.example .env
```

### 2. Запустить контейнеры

```bash
docker compose up -d --build
```

API доступен по адресу `http://localhost:8080`

### 3. Установка зависимостей и генерация ключа

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 4. Выполнить миграции

```bash
docker compose exec app php artisan migrate
```

### 5. Заполнить тестовыми данными

```bash
docker compose exec app php artisan db:seed
```

Тестовая учётная запись:

* Email: `test@example.com`
* Password: `password123`

## API

Все маршруты задач требуют аутентификации.

### Список задач

**GET** `/api/tasks`

Поддерживаемые query-параметры:

* `status` - фильтр по статусу: `new`, `in_progress`, `done`.
* `page` - номер страницы.
* `per_page` - количество записей на странице, по умолчанию 15.

### Получение задачи

**GET** `/api/tasks/{id}`

### Создание задачи

**POST** `/api/tasks`

### Обновление задачи

**PUT** `/api/tasks/{id}`

### Удаление задачи

**DELETE** `/api/tasks/{id}`

## HTTP-статусы

`200` | Запрос выполнен успешно
`201` | Задача создана
`204` | Задача удалена
`401` | Пользователь не аутентифицирован
`404` | Задача не найдена
`422` | Ошибка валидации
`500` | Внутренняя ошибка сервера

## Тестирование

Запуск тестов:

```bash
docker compose exec app php artisan test
```

## Postman

Коллекция запросов для ручного тестирования API находится в директории `docs/`

### Импорт коллекции

1. Открой Postman.
2. Нажми **Import**.
3. Выбери файл коллекции.
4. Выполни запрос получения токена `POST /api/token` и скопируй из ответа `token` в переменную `token` в настройках переменных коллекции (Variables), установи значение `base_url` - `http://localhost:8080/api` и сохрани изменения 
5. Выполняй запросы к API.

