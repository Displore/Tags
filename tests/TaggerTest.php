<?php

include 'model/Task.php';

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Displore\Tags\Tag;
use Test\Model\Task;

class TaggerTest extends TestCase
{
    use DatabaseTransactions;

    public static function setUpBeforeClass()
    {
        echo 'Testing tags requires a tasks table with a title (string) attribute.';
    }

    public function setUp()
    {
        parent::setUp();

        $this->tagger = app('Displore\Tags\Tagger');
    }

    public function test_it_can_tag_with_name()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $id = Tag::tag('Test')->first()->id;
        $task = Task::create(['title' => 'My task']);

        $this->tagger->tag($task, 'Test');

        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_tag_with_id()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $id = Tag::tag('Test')->first()->id;
        $task = Task::create(['title' => 'My task']);

        $this->tagger->tag($task, $id);

        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_untag_with_name()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $id = Tag::tag('Test')->first()->id;
        $task = Task::create(['title' => 'My task']);

        $this->tagger->tag($task, 'Test');

        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);

        $this->tagger->untag($task, 'Test');

        $this->dontSeeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_untag_with_id()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $id = Tag::tag('Test')->first()->id;
        $task = Task::create(['title' => 'My task']);

        $this->tagger->tag($task, $id);

        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);

        $this->tagger->untag($task, $id);

        $this->dontSeeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_sync_tags()
    {
        $this->tagger->create('Personal', 'Personal tag', 'testing');
        $personal = Tag::tag('Personal')->first()->id;
        $this->tagger->create('Work', 'Work tag', 'testing');
        $work = Tag::tag('Work')->first()->id;
        $task = Task::create(['title' => 'My task']);

        $this->tagger->tag($task, 'Personal');

        $this->seeInDatabase('taggables', [
            'tag_id' => $personal,
            'taggable_type' => 'Test\Model\Task',
        ]);

        $this->tagger->syncTags($task, ['Work']);

        $this->dontSeeInDatabase('taggables', [
            'tag_id' => $personal,
            'taggable_type' => 'Test\Model\Task',
        ]);
        $this->seeInDatabase('taggables', [
            'tag_id' => $work,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_create_a_tag()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');

        $this->seeInDatabase('tags', [
            'name' => 'Test',
            'category' => 'testing',
            'description' => 'Yet another tag',
        ]);
    }

    public function test_tag_or_create()
    {
        $task = Task::create(['title' => 'My task']);
        $this->tagger->tagOrCreate($task, 'Test', 'testing', 'Yet another tag');
        $id = Tag::tag('Test')->first()->id;

        $this->seeInDatabase('tags', [
            'name' => 'Test',
            'category' => 'testing',
            'description' => 'Yet another tag',
        ]);
        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
    }

    public function test_it_can_delete_a_tag()
    {
        // With name
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $this->seeInDatabase('tags', [
            'name' => 'Test',
        ]);

        $tag = $this->tagger->delete('Test');
        $this->dontSeeInDatabase('tags', [
            'name' => 'Test',
        ]);

        // With id
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $this->seeInDatabase('tags', [
            'name' => 'Test',
        ]);

        $id = Tag::tag('Test')->first()->id;
        $tag = $this->tagger->delete($id);
        $this->dontSeeInDatabase('tags', [
            'name' => 'Test',
        ]);

        // With attachments
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $task = Task::create(['title' => 'My task']);
        $id = Tag::tag('Test')->first()->id;
        $task->tag('Test');
        $this->seeInDatabase('tasks', [
            'title' => 'My Task',
        ]);
        $this->seeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);

        $this->tagger->delete($id);
        $this->dontSeeInDatabase('tags', [
            'name' => 'Test',
        ]);
        $this->dontSeeInDatabase('taggables', [
            'tag_id' => $id,
            'taggable_type' => 'Test\Model\Task',
        ]);
        $this->seeInDatabase('tasks', [
            'title' => 'My Task',
        ]);
    }

    public function test_can_get_models_with_tag()
    {
        $tag = $this->tagger->create('Test', 'testing', 'Yet another tag');
        $task = Task::create(['title' => 'My task']);
        $task->tag('Test');

        $tasksWithTag = $this->tagger->getWithTag('Test');
        $this->assertEquals('Test', $tasksWithTag->first()->tags()->first()->name);
    }
}
