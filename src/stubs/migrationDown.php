	public function down()
	{
		Schema::table('{{tableName}}', function($table) {
			{{methods}}
		});
	}