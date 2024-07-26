<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        "name","description","image_path","status","priority","due_date","assigned_user_id","created_by","updated_by","project_id"
    ];

    use HasFactory;
    
    public function project() {
        return $this->belongsTo(Project::class);
    }
    
    public function assignedUser() {
        //'assigned_user_id' is the foreign key in the tasks table that references the id column in the users table.
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    
    public function createdBy() {
        //'created_by' is the foreign key in the tasks table that references the id column in the users table.
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}