<?php

class {{TableName}}TableSeeder extends Seeder {

	public function run()
	{
		${{tableName}} = array(

		);

		DB::table('{{tableName}}')->insert(${{tableName}});
	}

}
