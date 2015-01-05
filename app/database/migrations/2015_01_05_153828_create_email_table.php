<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("emails", function (Blueprint $table)
		{
			$table->increments("id");
			$table->string("subject");
			$table->longText("plain");
			$table->longText("html");
			$table->integer("user_id");
			$table->string("status");

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
		Schema::dropIfExists("emails");
	}

}
