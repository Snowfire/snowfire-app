<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnowfireTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('snowfire_accounts', function($table)
        {
            $table->increments('id');
            $table->string('app_key')->unique();
            $table->string('site_url');
            $table->enum('state', [
                'INSTALLED',
                'UNINSTALLED'
            ])->default('INSTALLED');
            $table->timestamps();
        });

        Schema::create('snowfire_proxy', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('hash');
            $table->timestamp('valid_until');
            $table->string('to_url');
            $table->string('return_url');
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
		Schema::drop('snowfire_accounts');
        Schema::drop('snowfire_proxy');
	}

}
