<?php

namespace App;

class TaskList
{
    private $fileHandler;

    public function __construct($filename)
    {
        $this->fileHandler = new FileHandler($filename);
    }

    public function addTask(string $taskName, int $priority): void
    {
        $taskId = uniqid();
        $taskData = [
            'id' => $taskId,
            'name' => $taskName,
            'priority' => $priority,
            'completed' => false
        ];
        $tasks = $this->fileHandler->load();
        $tasks[$taskId] = $taskData;
        $this->fileHandler->save($tasks);
    }

    public function deleteTask(string $taskId): void
    {
        $tasks = $this->fileHandler->load();
        if (isset($tasks[$taskId])) {
            unset($tasks[$taskId]);
            $this->fileHandler->save($tasks);
        }
    }

    public function getTasks(): array
    {
        $tasks = $this->fileHandler->load();
        usort($tasks, function ($a, $b) {
            if ($a['priority']== $b['priority']) {
                return 0;
            }
            return ($a['priority'] > $b['priority']) ? -1 : 1;
        });
        return $tasks;
    }

    public function completeTask(string $taskId): void
    {
        $tasks = $this->fileHandler->load();
        if (isset($tasks[$taskId])) {
            $tasks[$taskId]['completed'] = true;
            $this->fileHandler->save($tasks);
        }
    }
}
