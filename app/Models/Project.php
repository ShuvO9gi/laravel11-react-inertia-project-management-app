<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // Define the relationship with the Task model
    public function tasks() {
        return $this->hasMany(Task::class);
    }
}