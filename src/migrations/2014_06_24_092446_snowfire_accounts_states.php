<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SnowfireAccountsStates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('snowfire_accounts', function($table)
		{
			$table->enum('state', [
				'INSTALLED',
				'UNINSTALLED'
			])->default('INSTALLED');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('snowfire_accounts', function($table)
		{
			$table->dropColumn('state');
		});
	}

}
