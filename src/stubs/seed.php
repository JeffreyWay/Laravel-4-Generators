<?php

class {{TableName}}TableSeeder extends Seeder {

	public function run()
	{
		${{tableName}} = [

		];

		DB::table('{{tableName}}')->insert(${{tableName}});
	}

}