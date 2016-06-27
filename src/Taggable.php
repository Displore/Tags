<?php

namespace Displore\Tags;

use Displore\Tags\Tag;

trait Taggable
{
    /**
     * Add a tag.
     * 
     * @param  string      $name
     * @param  string|null $category
     * @return bool
     */
    public function tag($name, $category = null)
    {
        $tag = Tag::category($category)->tag($name)->firstOrFail();

        return $this->tags()->attach($tag->id);
    }

    /**
     * Remove a tag.
     * 
     * @param  string      $tagToRemove
     * @param  string|null $category
     * @return bool
     */
    public function untag($name, $category = null)
    {
        $tag = Tag::category($category)->tag($name)->firstOrFail();

        return $this->tags()->detach($tag->id);
    }

    /**
     * Sync tags.
     * 
     * @param  array $tagsToSync
     * @return bool
     */
    public function syncTags(array $tagsToSync)
    {
        $tagKeys = Tag::whereIn('name', $tagsToSync)->get()->keyBy('id')->keys()->toArray();

        return $this->tags()->sync($tagKeys);
    }

    /**
     * Get all tags.
     * 
     * @return mixed
     */
    public function tags()
    {
        return $this->morphToMany('Displore\Tags\Tag', 'taggable');
    }
}
