<?php

namespace Displore\Tags;

use DB;
use Illuminate\Database\Eloquent\Model;

class Tagger
{
    /**
     * Add a tag to a taggable model.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|int                          $tag
     * @param string|null                         $category
     *
     * @return bool
     */
    public function tag(Model $model, $tag, $category = null)
    {
        if (is_int($tag)) {
            $this->tagWithId($model, $tag);
        } elseif (is_array($tag)) {
            foreach ($tag as $singleTag) {
                $this->tag($model, $singleTag, $category);
            }
        } else {
            $this->tagWithName($model, $tag, $category);
        }
    }

    /**
     * Add a tag by its id.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int                                 $id
     *
     * @return bool
     */
    public function tagWithId(Model $model, $id)
    {
        return $model->tags()->attach($id);
    }

    /**
     * Add a tag by its name.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $name
     * @param string|null                         $category
     *
     * @return bool
     */
    public function tagWithName(Model $model, $name, $category = null)
    {
        $tag = Tag::category($category)->tag($name)->firstOrFail();

        return $model->tags()->attach($tag->id);
    }

    /**
     * Add a tag, or create the tag if it doesn't exist.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $name
     * @param string|null                         $category
     * @param string|null                         $description
     *
     * @return bool
     */
    public function tagOrCreate(Model $model, $name, $category = null, $description = null)
    {
        $tag = Tag::category($category)->tag($name)->first();
        if (is_null($tag)) {
            $tag = Tag::create([
                'name' => $name,
                'category' => $category,
                'description' => $description,
            ]);
        }

        return $model->tags()->attach($tag->id);
    }

    /**
     * Remove a tag from a model.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string|int                          $tagToRemove
     * @param string|null                         $category
     *
     * @return bool
     */
    public function untag(Model $model, $tagToRemove, $category = null)
    {
        if (is_int($tagToRemove)) {
            $this->untagWithId($model, $tagToRemove);
        } elseif (is_array($tagToRemove)) {
            foreach ($tagToRemove as $singleTagToRemove) {
                $this->untag($model, $singleTagToRemove, $category);
            }
        } else {
            $this->untagWithName($model, $tagToRemove, $category);
        }
    }

    /**
     * Untag by its id.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int                                 $id
     *
     * @return bool
     */
    public function untagWithId(Model $model, $id)
    {
        return $model->tags()->detach($id);
    }

    /**
     * Untag by its name.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $name
     * @param string|null                         $category
     *
     * @return bool
     */
    public function untagWithName(Model $model, $name, $category = null)
    {
        $tag = Tag::category($category)->tag($name)->firstOrFail();

        return $model->tags()->detach($tag->id);
    }

    /**
     * Sync tags on a model.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $tagsToSync
     *
     * @return bool
     */
    public function syncTags(Model $model, array $tagsToSync)
    {
        $tagKeys = Tag::whereIn('name', $tagsToSync)->get()->keyBy('id')->keys()->toArray();

        return $model->tags()->sync($tagKeys);
    }

    /**
     * Create a new tag.
     * 
     * @param string      $name
     * @param string|null $category
     * @param string|null $description
     *
     * @return $this
     */
    public function create($name, $category = null, $description = null)
    {
        Tag::create([
            'name' => $name,
            'category' => $category,
            'description' => $description,
        ]);

        return $this;
    }

    /**
     * Delete a tag.
     * 
     * @param string|int  $tag
     * @param string|null $category
     *
     * @return bool
     */
    public function delete($tag, $category = null)
    {
        if (is_int($tag)) {
            $tag = Tag::findOrFail($tag);
            $this->detachForTag($tag);

            return $tag->delete();
        } else {
            $tag = Tag::category($category)->tag($tag)->firstOrFail();
            $this->detachForTag($tag);

            return $tag->delete();
        }
    }

    /**
     * Get all models with the given tag.
     * 
     * @param string|int  $tag
     * @param string|null $category
     *
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithTag($tag, $category = null)
    {
        if (is_int($tag)) {
            $tag = Tag::findOrFail($tag);

            $taggables = DB::table('taggables')
                            ->where('tag_id', $tag->id)
                            ->get();

            return $taggables->map(function ($item) {
                $class = new $item->taggable_type();

                return $class::find($item->taggable_id);
            });
        } else {
            $tag = Tag::category($category)->tag($tag)->firstOrFail();

            $taggables = DB::table('taggables')
                            ->where('tag_id', $tag->id)
                            ->get();

            return $taggables->map(function ($item) {
                $class = new $item->taggable_type();

                return $class::find($item->taggable_id);
            });
        }
    }

    /**
     * Detach the given tag from all its models.
     * 
     * @param \Displore\Core\Models\Tag $tag
     *
     * @return bool
     */
    public function detachForTag(Tag $tag)
    {
        return DB::table('taggables')->where('tag_id', $tag->id)->delete();
    }
}
