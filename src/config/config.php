<?php

return [

    /**
     * Where is the template for a model?
     */
    'model_template_path' => 'vendor/way/generators/src/Way/Generators/templates/model.txt',

    /**
     * Where do you put your models?
     */
    'model_target_path'   => app_path('models'),

    /**
     * Where is the template for a migration?
     */
    'migration_template_path' => 'vendor/way/generators/src/Way/Generators/templates/migration.txt',

    /**
     * Where do you put your migrations?
     */
    'migration_target_path'   => app_path('database/migrations'),

    /**
     * Where is the template for a database seeder?
     */
    'seed_template_path' => 'vendor/way/generators/src/Way/Generators/templates/seed.txt',

    /**
     * Where do you put your database table seeders?
     */
    'seed_target_path'   => app_path('database/seeds')

];