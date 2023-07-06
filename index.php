<?php
require 'vendor/autoload.php';

use App\TaskList;

// Создаем экземпляр класса TaskList, указывая имя файла для хранения задач
$taskList = new TaskList('tasks.json');

// Добавляем задачи
$taskList->addTask('Task 1', 2);
$taskList->addTask('Task 2', 1);
$taskList->addTask('Task 3', 3);

// Получаем все задачи
$tasks = $taskList->getTasks();
foreach ($tasks as $task) {
    echo $task['name'] . ' (Priority: ' . $task['priority'] . ')' . "<br/>";
}

// Отмечаем задачу с идентификатором $taskId как выполненную
$taskId = $tasks[0]['id'];
$taskList->completeTask($taskId);

// Удаляем задачу с идентификатором $taskId
$taskId = $tasks[1]['id'];
$taskList->deleteTask($taskId);
//

// Задача №3

//Создание подключения к базе данных
$host = 'localhost';
$dbName = 'mydb';
$username = 'root';
$password = 'password123';

$pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password);

// Запрос для получения данных из таблицы блогов с присоединением категорий и пользователей
$sql = "
    SELECT b.id, b.title, b.content, b.created_at, b.updated_at,
           u.id AS user_id, u.firstname, u.lastname, u.email,
           c.id AS category_id, c.title AS category_title
    FROM blogs b
    JOIN users u ON b.author_id = u.id
    JOIN blogs_categories bc ON b.id = bc.blog_id
    JOIN categories c ON bc.category_id = c.id
    WHERE b.deleted = false";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Формирование результирующего массива
$result = [];
foreach ($blogs as $blog) {
    $blogId = $blog['id'];

    // Создание объекта пользователя
    $user = [
        'id' => $blog['user_id'],
        'firstname' => $blog['firstname'],
        'lastname' => $blog['lastname'],
        'email' => $blog['email']
    ];

    // Создание объекта категории
    $category = [
        'id' =>$blog['category_id'],
        'title' => $blog['category_title']
    ];

    // Проверка, существует ли уже блог в результате, и добавление категории к соответствующему блогу
    if (isset($result[$blogId])) {
        $result[$blogId]['categories'][] = $category;
    } else {
        // Создание объекта блога и добавление категории и пользователя
        $result[$blogId] = [
            'id' => $blogId,
            'title' => $blog['title'],
            'content' => $blog['content'],
            'created_at' => $blog['created_at'],
            'updated_at' => $blog['updated_at'],
            'user' => $user,
            'categories' => [$category]
        ];
    }
}

// Вывод результатов
foreach ($result as $blog) {
    echo 'Blog ID: ' . $blog['id'] . "<br/>";
    echo 'Title: ' . $blog['title'] . "<br/>";
    echo 'Content: ' . $blog['content'] . "<br/>";
    echo 'Created at: ' . $blog['created_at'] . "<br/>";
    echo 'Updated at: ' . $blog['updated_at'] . "<br/>";
    echo 'User: ' . $blog['user']['firstname'] . ' ' . $blog['user']['lastname'] . ' (' . $blog['user']['email'] . ')' . "<br/>";
    echo 'Categories: ' . "<br/>";
    foreach ($blog['categories'] as $category) {
        echo '- ' . $category['title'] . "<br/>";
    }
    echo "<br/>";
}
