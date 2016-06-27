<?php

namespace Displore\Tags;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Tag extends Eloquent
{
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'category',
    ];

    /**
     * Eloquent Scope for tag categories.
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string                             $category
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeCategory($query, $category)
    {
        if ( ! is_null($category)) {
            return $query->where('category', '=', $category);
        }

        return $query;
    }

    public function scopeTag($query, $name)
    {
        return $query->where('name', '=', $name);
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
