<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
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

        //clearing 'success' message session
        // $success = session("success");
        // session()->forget("success");

        $success = session()->pull('success');

        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            "queryParams" => request()->query() ?: null,
            // "success" => session("success")
            // "success" => session()->pull("success"),
            "success" => $success,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $project = Project::all();
        $project = Project::query()->orderBy("name", "asc")->get();
        // $user = User::all();
        $user = User::query()->orderBy("name", "asc")->get();

        return inertia("Task/Create", [
            "projects" => ProjectResource::collection($project),
            "users" => UserResource::collection($user),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        //dd($data);
        /** @var $image Illuminate\Http\UploadedFile */
        $image = $data["image"] ?? null;
        $data["created_by"] = Auth::id();
        $data["updated_by"] = Auth::id();
        // dd($data);
        if($image) {
            $data["image_path"] = $image->store("project/".Str::random(), "public");
        }
        Task::create($data);
        return to_route("task.index")->with("success", "Task was created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return inertia("Task/Show", [
            "task" => new TaskResource($task),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $project = Project::query()->orderBy("name", "asc")->get();
        $user = User::query()->orderBy("name", "asc")->get();
        return inertia("Task/Edit", [
            "task" => new TaskResource($task),
            "projects" => ProjectResource::collection($project),
            "users" => UserResource::collection($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $data["updated_by"] = Auth::id();

        $image = $data["image"] ?? null;

        if($image) {
            if($task->image_path) {
                Storage::disk("public")->deleteDirectory(dirname($task->image_path));
            }
            $data["image_path"] = $image->store("project/".Str::random(), "public");
        }

        $task->update($data);

        return to_route("task.index")->with("success", "Task \"$task->name\" was updated successfully!");
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

        return to_route("task.index")->with("success", "Task \"$name\" was deleted successfully!");
    }

    /**
     * filter own task.
     */
    public function myTask() {
        $user = auth()->user();
        //dd($user);
        $query = Task::query()->where("assigned_user_id", $user->id);

        $sortField = request("sort_field", "created_at");
        $sortDirection = request("sort_direction", "desc");

        if(request("name")) {
            $query->where("name", "like", "%" . request("name") . "%");
        }

        if(request("status")) {
            $query->where("status", request("status"));
        }

        $task = $query->orderBy($sortField, $sortDirection)->paginate(10)->onEachSide(1);

        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($task),
            "queryParams" => request()->query() ?: null,
            "success" => session("success")
        ]);
    }

}