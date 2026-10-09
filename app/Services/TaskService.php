<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->paginate($filters);
    }

    public function getById(int $id): Task
    {
        return $this->taskRepository->findById($id);
    }

    public function create(array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->getById($id);

        return $this->taskRepository->update($task, $data);
    }

    public function delete(int $id): void
    {
        $task = $this->getById($id);

        $this->taskRepository->delete($task);
    }
}
