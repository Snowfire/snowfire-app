<?php namespace Snowfire\App\Storage;

use Illuminate\Database\Seeder as S;
use Illuminate\Database\Eloquent\Model;

class Seeder extends S {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        Model::unguard();

        AccountStorage::create([
            'app_key' => md5(time()),
            'site_url' => 'http://example.com',
            'state' => 'INSTALLED'
        ]);
	}

}
