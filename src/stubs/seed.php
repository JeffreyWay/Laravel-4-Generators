<?php

class {{TableName}}TableSeeder extends Seeder {

	public function run()
	{
		DB::table('{{tableName}}')->delete();
		
		${{tableName}} = array(

		);

		DB::table('{{tableName}}')->insert(${{tableName}});
	}

}
