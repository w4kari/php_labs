# Лабораторная работа №7 Шаблонизация `Бурцева Дарья, IA2403`

## Цель работы

Освоить принципы шаблонизации в PHP как с использованием нативных PHP-шаблонов, так и с применением готового шаблонизатора Twig. Улучшить структуру проекта за счёт разделения логики обработки данных и слоя представления.

## Задание

В рамках лабораторной работы требовалось продолжить проект из предыдущей лабораторной работы и выполнить следующие этапы:

1. Разделить проект на два слоя:
   - **логика**: обработка запросов, чтение и запись данных, валидация и подготовка переменных;
   - **представление**: отдельные шаблоны, отвечающие только за вывод данных.
2. Реализовать отображение данных с помощью **нативных PHP-шаблонов**.
3. Подключить шаблонизатор **Twig** через Composer и реализовать ту же функциональность с использованием Twig.
4. Применить возможности Twig для улучшения структуры шаблонов, в частности наследование и блоки.
5. Реализовать **собственный фильтр Twig**, решающий практическую задачу в проекте.

---

## Структура проекта

Фактическая структура разработанного проекта имеет следующий вид:

```text
lab7/
├── composer.json
├── composer.lock
├── data.txt
├── index.php
├── index_twig.php
├── list_jokes.php
├── list_jokes_twig.php
├── JokeValidator.php
├── ValidatorInterface.php
├── src/
│   ├── functions.php
│   ├── handlers.php
│   ├── render_native.php
│   └── twig.php
├── templates/
│   ├── native/
│   │   ├── layout.php
│   │   ├── form.php
│   │   └── list.php
│   └── twig/
│       ├── layout.twig
│       ├── form.twig
│       └── list.twig
├── images/
│   ├── форма.png
│   └── каталог.png
└── vendor/
    └── ...
```

---

## Используемые технологии

В работе использовались следующие средства:

- язык программирования **PHP 8.1+**
- шаблонизатор **Twig 3**
- менеджер зависимостей **Composer**
- встроенные возможности PHP для работы с файлами, JSON, датами и HTML-экранированием

Подключение Twig выполнено через файл `composer.json`:

```json
{
  "name": "php-labs/lab7",
  "type": "project",
  "require": {
    "php": ">=8.1",
    "twig/twig": "^3.0"
  }
}
```

---

## Ход выполнения работы

### 1. Точки входа приложения

#### 1.1. Главная страница с нативным PHP-шаблоном

Файл `index.php`:

```php
<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/render_native.php';

$viewData = handleJokeFormRequest($_SERVER['REQUEST_METHOD'], $_POST, __DIR__ . '/data.txt');
$viewData['title'] = 'Форма добавления анекдота';
$viewData['heading'] = 'Каталог анекдотов (Нативные PHP-шаблоны)';

renderNativeTemplate('form.php', $viewData);
```

В данном файле выполняются следующие действия:

- подключаются обработчики запроса и механизм рендеринга нативного шаблона
- вызывается функция `handleJokeFormRequest()`, которая подготавливает данные формы
- в массив представления добавляются заголовок страницы и заголовок блока
- подготовленные данные передаются в шаблон `form.php`

#### 1.2. Страница каталога с нативным PHP-шаблоном

Файл `list_jokes.php`:

```php
<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/render_native.php';

$viewData = handleJokesListRequest($_GET, __DIR__ . '/data.txt');
$viewData['title'] = 'Список анекдотов';
$viewData['heading'] = 'Каталог анекдотов (Нативные PHP-шаблоны)';

renderNativeTemplate('list.php', $viewData);
```

Этот файл формирует страницу со списком анекдотов и передаёт данные в нативный шаблон таблицы.

#### 1.3. Главная страница с Twig

Файл `index_twig.php`:

```php
<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/twig.php';

$viewData = handleJokeFormRequest($_SERVER['REQUEST_METHOD'], $_POST, __DIR__ . '/data.txt');
$twig = createTwigEnvironment();

echo $twig->render('form.twig', $viewData);
```

В этой версии логика обработки данных остаётся той же, но вместо нативного рендера используется метод `render()` объекта Twig.

#### 1.4. Страница каталога с Twig

Файл `list_jokes_twig.php`:

```php
<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/twig.php';

$viewData = handleJokesListRequest($_GET, __DIR__ . '/data.txt');
$viewData['nextOrder'] = toggleOrder($viewData['order']);
$twig = createTwigEnvironment();

echo $twig->render('list.twig', $viewData);
```

В данном файле дополнительно вычисляется значение `nextOrder`, которое затем используется в шаблоне Twig для формирования ссылок сортировки.

---

### 2. Общие функции проекта

Общие функции вынесены в файл `src/functions.php`.

#### 2.1. Константы категорий и рейтингов

```php
const CATEGORIES = [
    'short' => 'Короткий',
    'family' => 'Семейный',
    'school' => 'Школьный',
    'work' => 'Про работу',
    'animals' => 'Про животных',
    'classic' => 'Классический',
];

const RATINGS = ['0+', '12+', '16+', '18+'];
```

#### 2.2. Функция экранирования HTML

```php
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
```

#### 2.3. Вспомогательные функции

```php
function renderTags($tags): string
{
    if (is_array($tags)) {
        $normalized = [];
        foreach ($tags as $tag) {
            if (!is_string($tag)) {
                continue;
            }

            $trimmedTag = trim($tag);
            if ($trimmedTag !== '') {
                $normalized[] = $trimmedTag;
            }
        }

        return implode(', ', $normalized);
    }

    if (is_string($tags)) {
        return trim($tags);
    }

    return '';
}

function toggleOrder(string $currentOrder): string
{
    return strtolower($currentOrder) === 'asc' ? 'desc' : 'asc';
}

```

Назначение функций:

- `renderTags()` преобразует список тегов в строку;
- `toggleOrder()` переключает порядок сортировки между `asc` и `desc`.

#### 2.4. Работа с файлом данных

```php
function readJokesFromFile(string $filename): array
{
    if (!file_exists($filename)) {
        return [];
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    $jokes = [];
    foreach ($lines as $line) {
        $decoded = json_decode($line, true);
        if (is_array($decoded)) {
            $jokes[] = $decoded;
        }
    }

    return $jokes;
}

function appendJokeToFile(string $filename, array $validatedJoke): bool
{
    $jsonLine = json_encode($validatedJoke, JSON_UNESCAPED_UNICODE);
    if ($jsonLine === false) {
        return false;
    }

    $result = file_put_contents($filename, $jsonLine . PHP_EOL, FILE_APPEND | LOCK_EX);

    return $result !== false;
}
```

Анекдоты сохраняются построчно в файл `data.txt` в формате JSON. При чтении каждая строка преобразуется обратно в массив.

#### 2.5. Сортировка записей

```php
function sortJokes(array $jokes, string $sortBy, string $order): array
{
    $allowedFields = ['title', 'category', 'author', 'publish_date', 'created_at', 'updated_at', 'rating'];

    if (!in_array($sortBy, $allowedFields, true)) {
        $sortBy = 'created_at';
    }

    $order = strtolower($order) === 'desc' ? 'desc' : 'asc';

    usort(
        $jokes,
        static function (array $a, array $b) use ($sortBy, $order): int {
            $valueA = $a[$sortBy] ?? '';
            $valueB = $b[$sortBy] ?? '';
            $result = strcmp((string)$valueA, (string)$valueB);
            return $order === 'desc' ? -$result : $result;
        }
    );

    return $jokes;
}
```

Функция сортирует записи по заданному полю и направлению.

---

### 3. Обработчики запросов

Файл `src/handlers.php` содержит две основные функции обработки запросов.

#### 3.1. Обработчик формы

```php
function handleJokeFormRequest(string $requestMethod, array $post, string $dataFilePath): array
{
    $fields = [
        'title',
        'author',
        'content',
        'category',
        'rating',
        'publish_date',
        'created_at',
        'updated_at',
    ];

    $formData = array_fill_keys($fields, '');
    $formData['tags'] = ['', '', ''];
    $errors = [];
    $successMessage = '';

    if ($requestMethod === 'POST') {
        foreach ($fields as $field) {
            $value = $post[$field] ?? '';
            $formData[$field] = is_string($value) ? trim($value) : '';
        }

        $rawTags = $post['tags'] ?? [];
        if (!is_array($rawTags)) {
            $rawTags = [$rawTags];
        }

        $preparedTags = [];
        foreach ($rawTags as $tag) {
            if (!is_string($tag)) {
                continue;
            }

            $trimmedTag = trim($tag);
            if ($trimmedTag !== '') {
                $preparedTags[] = $trimmedTag;
            }
        }

        while (count($preparedTags) < 3) {
            $preparedTags[] = '';
        }

        $formData['tags'] = $preparedTags;

        $validator = new JokeValidator($formData);

        if ($validator->validate()) {
            if (appendJokeToFile($dataFilePath, $validator->validated())) {
                $successMessage = 'Анекдот успешно сохранен.';
                $formData = array_fill_keys($fields, '');
                $formData['tags'] = ['', '', ''];
            } else {
                $errors['_form'][] = 'Не удалось сохранить данные в файл.';
            }
        } else {
            $errors = $validator->errors();
        }
    }

    return [
        'formData' => $formData,
        'errors' => $errors,
        'successMessage' => $successMessage,
        'categories' => CATEGORIES,
        'ratings' => RATINGS,
    ];
}
```

Данный обработчик:

- подготавливает начальные значения формы
- получает данные из `POST`
- нормализует теги
- создаёт валидатор `JokeValidator`
- при успешной валидации сохраняет запись в файл
- при ошибках возвращает массив ошибок в шаблон

#### 3.2. Обработчик списка

```php
function handleJokesListRequest(array $query, string $dataFilePath): array
{
    $sortBy = isset($query['sort']) && is_string($query['sort']) ? $query['sort'] : 'created_at';
    $order = isset($query['order']) && is_string($query['order']) ? $query['order'] : 'asc';

    $jokes = readJokesFromFile($dataFilePath);
    $jokes = sortJokes($jokes, $sortBy, $order);

    return [
        'jokes' => $jokes,
        'sortBy' => $sortBy,
        'order' => $order,
    ];
}
```

Этот обработчик отвечает за чтение списка анекдотов и его сортировку перед передачей в шаблон.

---

### 4. Валидация данных

Для проекта создан интерфейс `ValidatorInterface`:

```php
<?php
declare(strict_types=1);

interface ValidatorInterface
{
    public function validate(): bool;
    public function errors(): array;
    public function validated(): array;
}
```

На основе этого интерфейса реализован класс `JokeValidator`.

#### 4.1. Подготовка данных

```php
private function sanitize(array $data): array
{
    $clean = [];

    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $clean[$key] = trim($value);
            continue;
        }

        if (is_array($value)) {
            $clean[$key] = array_map(
                static fn ($item) => is_string($item) ? trim($item) : $item,
                $value
            );
            continue;
        }

        $clean[$key] = $value;
    }

    return $clean;
}
```

Функция очищает входные данные: удаляет лишние пробелы и нормализует массивы.

#### 4.2. Нормализация тегов

```php
private function normalizeTags($rawTags): array
{
    if (is_string($rawTags)) {
        $rawTags = array_map('trim', explode(',', $rawTags));
    }

    if (!is_array($rawTags)) {
        return [];
    }

    $tags = [];
    foreach ($rawTags as $tag) {
        if (!is_string($tag)) {
            continue;
        }

        $trimmedTag = trim($tag);
        if ($trimmedTag !== '') {
            $tags[] = $trimmedTag;
        }
    }

    return array_values(array_unique($tags));
}
```

Теги очищаются, пустые значения удаляются, а дубликаты исключаются.

#### 4.3. Основная валидация

```php
public function validate(): bool
{
    $title = (string)($this->data['title'] ?? '');
    $content = (string)($this->data['content'] ?? '');
    $category = (string)($this->data['category'] ?? '');
    $author = (string)($this->data['author'] ?? '');
    $publishDate = (string)($this->data['publish_date'] ?? '');
    $createdAt = (string)($this->data['created_at'] ?? '');
    $updatedAt = (string)($this->data['updated_at'] ?? '');
    $rating = (string)($this->data['rating'] ?? '');
    $tags = $this->normalizeTags($this->data['tags'] ?? []);
    $today = (new \DateTimeImmutable('today'))->format('Y-m-d');

    if ($title === '') {
        $this->addError('title', 'Название обязательно для заполнения.');
    } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
        $this->addError('title', 'Название должно содержать от 3 до 100 символов.');
    }

    if ($content === '') {
        $this->addError('content', 'Текст анекдота обязателен.');
    } elseif (mb_strlen($content) < 10 || mb_strlen($content) > 2000) {
        $this->addError('content', 'Текст анекдота должен содержать от 10 до 2000 символов.');
    }

    if ($category === '') {
        $this->addError('category', 'Категория обязательна.');
    } elseif (!in_array($category, $this->allowedCategories, true)) {
        $this->addError('category', 'Недопустимая категория.');
    }

    if ($author === '') {
        $this->addError('author', 'Автор обязателен.');
    }

    if ($publishDate === '' || !$this->isValidDate($publishDate)) {
        $this->addError('publish_date', 'Дата публикации указана неверно.');
    } elseif ($publishDate > $today) {
        $this->addError('publish_date', 'Дата публикации не может быть в будущем.');
    }

    if ($createdAt === '' || !$this->isValidDate($createdAt)) {
        $this->addError('created_at', 'Дата создания указана неверно.');
    } elseif ($createdAt > $today) {
        $this->addError('created_at', 'Дата создания не может быть в будущем.');
    }

    if ($updatedAt === '' || !$this->isValidDate($updatedAt)) {
        $this->addError('updated_at', 'Дата обновления указана неверно.');
    } elseif ($updatedAt > $today) {
        $this->addError('updated_at', 'Дата обновления не может быть в будущем.');
    }

    if ($rating === '') {
        $this->addError('rating', 'Возрастной рейтинг обязателен.');
    } elseif (!in_array($rating, $this->allowedRatings, true)) {
        $this->addError('rating', 'Недопустимый возрастной рейтинг.');
    }

    if (empty($this->errors)) {
        $this->validated = [
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'author' => $author,
            'publish_date' => $publishDate,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'rating' => $rating,
            'tags' => $tags,
        ];
    }

    return empty($this->errors);
}
```

В ходе валидации проверяются:

- заполненность обязательных полей
- длина названия и текста анекдота
- допустимость категории и возрастного рейтинга
- корректность формата дат
- отсутствие дат в будущем

---

### 5. Подключение и настройка Twig

Реализация Twig находится в файле `src/twig.php`.

```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

function createTwigEnvironment(): Environment
{
    $autoloadFile = __DIR__ . '/../vendor/autoload.php';

    if (!file_exists($autoloadFile)) {
        http_response_code(500);
        echo 'Twig не установлен. Выполните: composer install';
        exit;
    }

    require_once $autoloadFile;

    $loader = new FilesystemLoader(__DIR__ . '/../templates/twig');
    $twig = new Environment($loader, [
        'cache' => false,
        'strict_variables' => false,
        'autoescape' => 'html',
    ]);

    $twig->addFilter(
        new TwigFilter(
            'human_date',
            static function (string $date): string {
                $dateTime = DateTime::createFromFormat('Y-m-d', $date);
                if ($dateTime === false || $dateTime->format('Y-m-d') !== $date) {
                    return $date;
                }

                $today = new DateTimeImmutable('today');
                $updated = DateTimeImmutable::createFromFormat('Y-m-d', $date);
                if ($updated === false) {
                    return $date;
                }

                $days = (int)$updated->diff($today)->format('%a');
                $label = $days === 1 ? 'день' : 'дней';

                return $date . ' (' . $days . ' ' . $label . ' назад)';
            }
        )
    );

    return $twig;
}
```

В данном файле выполняются три ключевые задачи:

1. Подключается автозагрузчик Composer
2. Создаётся объект `Twig\Environment` с загрузчиком `FilesystemLoader`
3. Регистрируется собственный фильтр `human_date`

Параметры Twig выбраны следующим образом:

- `cache => false` отключает кэширование шаблонов в учебной среде
- `strict_variables => false` позволяет не прерывать выполнение при отсутствии некоторых переменных
- `autoescape => 'html'` автоматически экранирует HTML-вывод

---

### 7. Реализация Twig-шаблонов

#### 7.1. Базовый Twig-макет

Файл `templates/twig/layout.twig`:

```
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Каталог анекдотов{% endblock %}</title>
    <style>
        /* стили страницы */
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{% block heading %}Каталог анекдотов{% endblock %}</h1>
    </div>
    <div class="body">
        {% block body %}{% endblock %}
    </div>
</div>
</body>
</html>
```

В данном шаблоне используются блоки `title`, `heading` и `body`, которые затем переопределяются в дочерних шаблонах.

#### 7.2. Twig-шаблон формы

Файл `templates/twig/form.twig`:

```twig
{% extends "layout.twig" %}

{% block title %}Форма добавления анекдота (Twig){% endblock %}
{% block heading %}Каталог анекдотов (Twig){% endblock %}

{% block body %}
    {% if successMessage %}
        <div class="alert alert-success">{{ successMessage }}</div>
    {% endif %}

    <form action="" method="POST" novalidate>
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Название анекдота</label>
                <input type="text" id="title" name="title" required minlength="3" maxlength="100"
                       value="{{ formData.title }}"
                       class="{{ errors.title is defined and errors.title is not empty ? 'input-error' : '' }}">
                {% if errors.title is defined and errors.title is not empty %}
                    {% for error in errors.title %}
                        <div class="field-error">{{ error }}</div>
                    {% endfor %}
                {% endif %}
            </div>
        </div>
    </form>
{% endblock %}
```

Здесь используются основные конструкции Twig:

- `{{ ... }}` для вывода значений;
- `{% if ... %}` для условий;
- `{% for ... %}` для циклов;
- `{% extends %}` и `{% block %}` для наследования шаблонов.

#### 7.3. Twig-шаблон списка

Файл `templates/twig/list.twig`:

```twig
{% extends "layout.twig" %}

{% block title %}Список анекдотов (Twig){% endblock %}
{% block heading %}Каталог анекдотов (Twig){% endblock %}

{% block body %}
    <p class="sort-info">
        Текущая сортировка: <strong>{{ sortBy }}</strong>, порядок: <strong>{{ order }}</strong>
    </p>

    {% if jokes is empty %}
        <div class="empty">Пока нет сохраненных анекдотов.</div>
    {% else %}
        <table>
            <tbody>
            {% for joke in jokes %}
                <tr>
                    <td>{{ joke.title|default('') }}</td>
                    <td>{{ joke.category|default('') }}</td>
                    <td class="content-cell">{{ joke.content|default('') }}</td>
                    <td>{{ joke.author|default('') }}</td>
                    <td>{{ joke.updated_at|default('')|human_date }}</td>
                    <td>{{ joke.tags is iterable ? joke.tags|join(', ') : joke.tags|default('') }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endif %}
{% endblock %}
```

---

### 8. Собственный фильтр Twig

Дополнительное задание лабораторной работы выполнено за счёт создания собственного фильтра Twig `human_date`.

Фильтр реализован в `src/twig.php` и решает практическую задачу: показывает дату обновления записи и одновременно указывает, сколько дней прошло с момента последнего обновления.

Пример результата работы фильтра:

```text
2026-03-31 (16 дней назад)
```

---

## Ответы на контрольные вопросы

### 1. В чём отличие нативных PHP-шаблонов от шаблонизатора Twig? Какие преимущества и недостатки у каждого подхода?

**Нативные PHP-шаблоны** представляют собой обычные `.php`-файлы, в которых можно сразу использовать переменные, условия, циклы и функции PHP.

**Преимущества нативных шаблонов:**

- не требуют установки сторонних библиотек
- работают быстро и просто
- хорошо подходят для небольших проектов
- позволяют использовать весь синтаксис PHP

**Недостатки нативных шаблонов:**

- при неаккуратной разработке логика легко смешивается с представлением
- шаблоны становятся менее читаемыми
- нет встроенных средств наследования, фильтров и удобного синтаксиса представления
- экранирование нужно контролировать вручную

**Twig** является отдельным шаблонизатором с собственным синтаксисом.

**Преимущества Twig:**

- шаблоны легче читать
- поддерживается наследование шаблонов
- есть блоки, фильтры, функции и автоэкранирование
- проще ограничить логику внутри шаблона и сделать код представления чище

**Недостатки Twig:**

- необходимо подключать библиотеку через Composer
- требуется изучить отдельный синтаксис
- для очень простых проектов может быть избыточным

### 2. Зачем разделять логику и представление в проекте? Какие проблемы могут возникнуть, если смешивать их в одном файле?

Разделение логики и представления делает приложение более понятным и сопровождаемым. Логика отвечает за обработку данных, работу с файлами, валидацию и вычисления, а представление отвечает только за отображение уже подготовленной информации.

Если смешивать всё в одном файле, возникают такие проблемы как: усложняется поиск ошибок; повторное использование частей программы становится неудобным; изменение внешнего вида может случайно затронуть обработку данных, и наоборот.

### 3. Что такое наследование шаблонов в Twig? Как работают `{% extends %}` и `{% block %}`?

Наследование шаблонов в Twig позволяет создать один базовый шаблон, содержащий общую HTML-структуру страницы, и несколько дочерних шаблонов, которые подставляют только изменяемые части.

- `{% extends "layout.twig" %}` означает, что текущий шаблон наследуется от базового шаблона `layout.twig`.
- `{% block body %} ... {% endblock %}` задаёт именованный блок, который можно переопределить в дочернем шаблоне.

Такой подход позволяет избежать дублирования общей структуры страницы и упрощает поддержку интерфейса.

---

## Вывод

В ходе выполнения лабораторной работы были изучены и практически применены два подхода к шаблонизации в PHP: нативные PHP-шаблоны и шаблонизатор Twig.

Выполненное задание показало, что разделение проекта на слой логики и слой представления делает код более структурированным, понятным и удобным для сопровождения.

Также был подключён Twig через Composer, реализовано наследование шаблонов с помощью `{% extends %}` и `{% block %}`, а в качестве дополнительного задания создан собственный фильтр `human_date` для более удобного отображения даты обновления записей.

Таким образом, цель лабораторной работы достигнута: в проекте реализованы оба варианта шаблонизации, обеспечено разделение логики и представления, а также продемонстрированы расширенные возможности Twig.

---

## Библиография

1. Курс Moodle "Advanced Web Development (PHP)"  
https://elearning.usm.md/course/view.php?id=7161
2. extends - Tags - Documentation - Twig PHP - Symfony  
https://twig.symfony.com/doc/3.x/tags/extends.html
3. Использование composer в проекте php для начинающих  
https://habr.com/ru/articles/823846/
4. Что такое шаблонизатор Twig и зачем он нужен?  
https://racurs.agency/blog/programming/chto-takoye-shablonizator-twig-i-zachem-on-nuzhen/