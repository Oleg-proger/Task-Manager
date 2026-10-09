<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexTaskRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();

        $tasks = $this->taskService->getAll($filters);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $task): TaskResource
    {
        return new TaskResource($this->taskService->getById($task));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, int $task): TaskResource
    {
        $updatedTask = $this->taskService->update(
            $task,
            $request->validated()
        );

        return new TaskResource($updatedTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $task): Response
    {
        $this->taskService->delete($task);

        return response()->noContent();
    }
}
