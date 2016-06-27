<?php

namespace Test\Model;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use \Displore\Tags\Taggable;

    public $table = 'tasks';

    public $fillable = ['title'];
}
