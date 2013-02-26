	public function up()
	{
		Schema::table('{{tableName}}', function($table) {
			{{methods}}
		});
	}