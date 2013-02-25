This Laravel 4 package provides a variety of generators to speed up your development process. These generators include:

- `generate:model`
- `generate:seed`
- `generate:test`
- `generate:view`
- `generate:migration`
- `generate:resource`

## Prefer a Video Walk-through?

[See here.](http://tutsplus.s3.amazonaws.com/tutspremium/courses_$folder$/WhatsNewInLaravel4/9-Generators.mp4)

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `way/generators`.

    "require": {
		"laravel/framework": "4.0.*",
		"way/generators": "dev-master"
	}

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Way\Generators\GeneratorsServiceProvider'

That's it! You're all set to go. Run the `artisan` command from the Terminal to see the new `generate` commands.

    php artisan

## Usage

Think of generators as an easy way to speed up your workflow. Rather than opening the models directory, creating a new file, saving it, and adding the class, you can simply run a single generate command.

- [Migrations](#migrations)
- [Models](#models)
- [Tests](#tests)
- [Views](#views)
- [Seeds](#controllers)
- [Resources](#resources)

### Migrations

Laravel 4 offers a migration generator, but it stops just short of creating the schema (or the fields for the table). Let's review a couple examples, using `generate:migration`.

    php artisan generate:migration create_post_table

If we don't specify the `fields` option, the following file will be created within `app/database/migrations`.

```php
<?php

use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration {

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::create('post', function($table)
	  {
	    $table->increments('id');

	    $table->timestamps();
	  });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	  Schema::drop('post');
	}

}
```

Notice that the generator is smart enough to detect that you're trying to create a table. When naming your migrations, make them as description as possible. The migration generator will detect the first word in your migration name and do its best to determine how to proceed. As such, for `create_posts_table`, the keyword is "create," which means that we should prepare the necessary schema to create a table.

If you instead use a migration name along the lines of `add_user_id_to_posts_table`, in that case, the keyword is "add," signaling that we intend to add rows to an existing table. Let's see what that generates.

    php artisan generate:migration add_user_id_to_posts_table

This will prepare the following boilerplate:

```php
<?php

use Illuminate\Database\Migrations\Migration;

class AddUserIdToPostsTable extends Migration {

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::table('posts', function($table)
	  {

	  });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::table('posts', function($table)
	  {

	  });
	}

}
```

Notice how, this time, we're not doing `Schema::create`.

#### Keywords

When writing migration names, use the following keywords to provide hints for the generator.

- `create` or `make` (`create_users_table`)
- `add` or `insert` (`add_user_id_to_posts_table`)
- `remove` or `drop` (`remove_user_id_from_posts_table`)

#### Generating Schema

This is pretty nice, but let's take things a step further and also generate the schema, using the `fields` option.

    php artisan generate:migration create_posts_table --fields="title:string, body:text"

Before we decipher this new option, let's see the output:

```php
<?php

use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::create('posts', function($table)
	  {
	    $table->increments('id');
	    $table->string('title');
		$table->text('body');
	    $table->timestamps();
	  });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	  Schema::drop('posts');
	}

}
```

Nice! A few things to notice here:

- The generator will automatically set the `id` as the primary key.
- It also will add the timestamps, as that's more common than not.
- It parsed the `fields` options, and added those fields.
- The drop method is smart enough to realize that, in reverse, the table should be dropped entirely.

To declare fields, use a comma-separated list of key:value pairs, where `key` is the name of the field, and `value` is the [column type](http://four.laravel.com/docs/schema#adding-columns). Here are some examples:

- `--fields="first:string, last:string"`
- `--fields="age:integer, yob:date"`

As a final demonstration, let's run a migration to remove the `completed` field from a `tasks` table.

    php artisan generate:migration remove_completed_from_tasks_table --fields="completed:boolean"

This time, as we're using the "remove" keyword, the generator understands that it should drop a column, and add it back in the `down()` method.

```php
<?php

use Illuminate\Database\Migrations\Migration;

class RemoveCompletedFromTasksTable extends Migration {

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::table('tasks', function($table)
	  {
	    $table->dropColumn('completed');
	  });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::table('tasks', function($table)
	  {
	    $table->boolean('completed');
	  });
	}

}
```

### Models

    php artisan generate:model Post

This will create the file, `app/models/Post.php` and insert the following boilerplate:

```php
<?php

class Post extends Eloquent {

}
```


### Tests

    php artisan generate:test PostsTest

This will generate the file, `app/tests/PostsTest.php` and fill it with a starting template:

```php
<?php

class PostsTest extends TestCase {
    public function test()
	{

	}
}
```

Should you need to place this file within a subdirectory (or somewhere else in the `app` folder), use the `--path` option, like so:

    php artisan generate:test PostsTest --path=tests/controllers

Now, `PostsTest.php` will be placed in `app/tests/controllers/`. Please note that, if the designated directory does not exist, it will be created recursively for you.

If you pass a `--controller=posts` flag, then the generator will assume that you're writing tests for your controller, and will give you some extra boilerplate to get you started.

```php
<?php

class PostsTest extends TestCase {
    public function testAll()
	{
		$response = $this->call('GET', 'posts');
		$this->assertTrue($response->isOk());
	}

	public function testShow()
	{
		$response = $this->call('GET', 'posts/1');
		$this->assertTrue($response->isOk());
	}

	public function testCreate()
	{
		$response = $this->call('GET', 'posts/create');
		$this->assertTrue($response->isOk());
	}

	public function testEdit()
	{
		$response = $this->call('GET', 'posts/1/edit');
		$this->assertTrue($response->isOk());
	}
}
```


### Views

    php artisan generate:view dog

This command will generate `app/views/dog.blade.php` and a simple string, for convenience.

    The dog.blade.php view.

As with all of the commands, you may specify a `--path` option to place this file elsewhere.

    php artisan generate:view index --path=views/dogs

Now, we get: `app/views/dogs/index.blade.php`.

### Seeds

Laravel 4 provides us with a flexible way to seed new tables.

    php artisan generate:seed dogs

Set the argument to the name of the table that you'd like a seed file for. This will generate `app/database/seeds/DogsTableSeeder.php` and populate it with:

```php
<?php

class DogsTableSeeder extends Seeder {

  public function run()
  {
    $Dogs = [

    ];

    DB::table('Dogs')->insert($Dogs);
  }

}
```

This command will also update `app/database/seeds/DatabaseSeeder.php` to include a call to this new seed class, as required by Laravel.

To fully seed the `dogs` table:

- Within the `$Dogs` array, add any number of arrays, containing the necessary rows.
- Return to the Terminal and run Laravel's `db:seed command` (`php artisan db:seed`).

### Resources

Think of the resource generator as the big enchilda. It calls all of its sibling generate commands. Assuming the following command:

    php artisan generate:resource dog --fields="name:string"

The following actions will take place:

- Creates a `create_dogs_table` migration, with a name column.
- Creates a `Dog.php` model.
- Creates a `views/dogs` folder, containing the `index`, `show`, `create`, and `edit` views.
- Creates a `database/seeds/DogsTableSeeder.php` seed file.
- Updates `DatabaseSeeder.php` to run `DogsTableSeeder`
- Creates `controllers/DogsController.php`, and fills it with restful methods.
- Updates `routes.php` to include: `Route::resource('dogs', 'DogsController')`.
- Creates a `tests/controllers/DogsControllerTest.php` file, and fills it with some boilerplate tests to get you started.

> Please note that the resource name is singular - the same as how you would name your model.

#### Workflow

Let's create a resource for displaying dogs in a restful way.

    php artisan generate:resource dog --fields="name:string, age:integer"
    composer dump-autoload

Next, we'll seed this new `dogs` table. Open `database/seeds/DogsTableSeeder.php` and add a couple of rows. Remember, you only need to edit the `$Dogs` array within this file.

    $Dogs = [
        ['name' => 'Sparky', 'age' => 5],
        ['name' => 'Joe', 'age' => 11]
    ];

Now, we migrate the database and seed the `dogs` table.

    php artisan migrate
    php artisan db:seed

Finally, let's display these two dogs, when accessing the `dogs/` route. Edit `controllers/DogsController.php`, and update the `index` method, like so:

    public function index()
    {
        return View::make('dogs.index')
    		->with('dogs', Dog::all());
    }

The last step is to update the view to display each of the posts that was passed to it. Open `views/dogs/index.blade.php` and add:

    <ul>
        @foreach($dogs as $dog)
    		<li>{{ $dog->name }} : {{ $dog->age }}</li>
    	@endforeach
    </ul>

Okay, okay, we're not using a layout file with the proper HTML. Who cares; this is just an example, fool.

Anyhow, we're all set. Run the server, and browse to `localhost:8000/dogs` to view your list.

    php artisan serve

- Sparky : 5
- Joe : 11

Isn't that way faster than manually doing all of that writing? To finish up, let's run the tests to make sure that everything is working, as expected.

    phpunit

And...it's green!
