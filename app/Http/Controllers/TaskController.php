<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Task::query();

        $sortField = request("sort_field", "created_at");
        $sortDirection = request("sort_direction", "desc");

        if (request("name")) {
            $query->where("name", "like", "%" . request("name") . "%");
        }
        
        if (request("status")) {
            $query->where("status", request("status"));
        }
        
        $tasks = $query->orderBy($sortField, $sortDirection)->paginate(10)->onEachSide(1);

        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            "queryParams" => request()->query() ?: null
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia("Task/Index");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        /** @var $image Illuminate\Http\UploadedFile */
        $image = $data["image"] ?? null;
        $data["created_by"] = Auth::id();
        $data["updated_by"] = Auth::id();
        // dd($data);
        if($image) {
            $data["image_path"] = $image->store("project/".Str::random(), "public");
        }
        Task::create($data);
        return to_route("project.index")->with("success", "Project was created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $name = $task->name;
        
        $task->delete();

        if($task->image_path) {
            Storage::disk("public")->deleteDirectory(dirname($task->image_path));
        }

        return to_route("project.index")->with("success", "Project \"$name\" was deleted successfully!");
    }
}