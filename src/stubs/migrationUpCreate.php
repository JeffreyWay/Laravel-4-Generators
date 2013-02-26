	public function up()
	{
		Schema::create('{{tableName}}', function($table) {
			$table->increments('id');
			{{methods}}
			$table->timestamps();
		});
	}