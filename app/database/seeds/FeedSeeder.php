<?php

class FeedSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		DB::table('users')->truncate();
		DB::table('feeds')->truncate();
		DB::table('users_feeds')->truncate();
		DB::table('emails')->truncate();

		$userNeil = User::create([
			'email' => 'neil@pebbsy.com',
		]);

		$this->command->info('User seeding complete');

		$dogFeed = Feed::create([
			'name'  => "Craiglist dogs",
			"description" => "Craiglist dogs (London)",
			"uri" => 'http://london.craigslist.co.uk/search/pet?query=dog&sort=date&format=rss',
			'ttl' => 120,
		]);

		$catFeed = Feed::create([
			'name'  => "Craiglist cats",
			"description" => "Craiglist cats (London)",
			"uri" => 'http://london.craigslist.co.uk/search/pet?query=cats&sort=date&format=rss',
			"ttl" => 120
		]);


		$this->command->info('Feed seeding complete');



		$userNeil->feeds()->attach($dogFeed->id);
		$userNeil->feeds()->attach($catFeed->id);

		$this->command->info('User feed seeding complete.');

	}



}
