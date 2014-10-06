# Fast Workflow in Laravel With Custom Generators
# 使用自定义代码生成工具快速进行Laravel开发

[![Build Status](https://travis-ci.org/JeffreyWay/Laravel-4-Generators.png?branch=master)](https://travis-ci.org/JeffreyWay/Laravel-4-Generators)

This Laravel package provides a variety of generators to speed up your development process. These generators include:
这个Laravle包提供了一种代码生成器，使得你可以加速你的开发进程，这些生成器包括：

- `generate:model`   - 模型生成器
- `generate:view`    - 视图生成器
- `generate:controller`  - 控制器生成器
- `generate:seed`    -
- `generate:migration`   -
- `generate:pivot`
- `generate:resource`
- `generate:scaffold`


## Installation
## 安装

> [Want a 5-minute video overview?](https://dl.dropboxusercontent.com/u/774859/Work/Laravel-4-Generators/Get-Started-With-Laravel-Custom-Generators.mp4)
> [需要一个五分钟教程视频吗?](https://dl.dropboxusercontent.com/u/774859/Work/Laravel-4-Generators/Get-Started-With-Laravel-Custom-Generators.mp4)


## Laravel 4.2 and Below
## Laravel 4.2 或者更低的版本

使用Composer安装这个包，编辑你项目的`composer.json`文件，在require中添加`way/generators`

    "require-dev": {
		"way/generators": "~2.0"
	}

然后，在命令行下执行composer update：

    composer update --dev

一旦这个操作完成，就只需要最后一步，在配置文件中加入服务提供者。打开`app/config/app.php`文件，添加一个新的记录到providers数组中.

    'Way\Generators\GeneratorsServiceProvider'

这样就可以了，你已经安装完成并可以运行这个包了。运行artisan命令行则可以在终端上看到generate相关命令。

    php artisan


That's it! You're all set to go. Run the `artisan` command from the Terminal to see the new `generate` commands.

    php artisan

## Laravel 5.0 或者更高版本

使用Composer安装这个包，编辑你项目的`composer.json`文件，在require中添加`way/generators`

	"require-dev": {
		"way/generators": "~3.0"
	}
由于在Laravel高版本中默认文件夹结构，需要3.0或者更高的版本，才能适应5.0版本以上的Laravel

然后，在命令行下执行composer update：

    composer update --dev

一旦这个操作完成，就只需要最后一步，在配置文件中加入服务提供者。打开`app/config/app.php`文件，添加一个新的记录到providers数组中.

    'Way\Generators\GeneratorsServiceProvider'

这样就可以了，你已经安装完成并可以运行这个包了。运行artisan命令行则可以在终端上看到generate相关命令。

    php artisan

## 使用示例

想象一下使用一个生成器加速你的工作流。而不是打开models文件夹，创建一个新的文件，保存它，并且在文件中添加一个class，你可以简单的运行一个生成器命令即可完成这一系列动作。

- [Migrations](#migrations)
- [Models](#models)
- [Views](#views)
- [Seeds](#seeds)
- [Pivot](#pivot)
- [Resources](#resources)
- [Scaffolding](#scaffolding)
- [Configuration](#configuration)

### 迁移

Laravel offers a migration generator, but it stops just short of creating the schema (or the fields for the table). Let's review a couple examples, using `generate:migration`.

    php artisan generate:migration create_posts_table

If we don't specify the `fields` option, the following file will be created within `app/database/migrations`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('posts', function(Blueprint $table) {
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
	    Schema::drop('posts');
	}

}

```

Notice that the generator is smart enough to detect that you're trying to create a table. When naming your migrations, make them as descriptive as possible. The migration generator will detect the first word in your migration name and do its best to determine how to proceed. As such, for `create_posts_table`, the keyword is "create," which means that we should prepare the necessary schema to create a table.

If you instead use a migration name along the lines of `add_user_id_to_posts_table`, in that case, the keyword is "add," signaling that we intend to add rows to an existing table. Let's see what that generates.

    php artisan generate:migration add_user_id_to_posts_table

This will prepare the following boilerplate:




















