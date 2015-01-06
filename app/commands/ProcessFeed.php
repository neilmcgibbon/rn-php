<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ProcessFeed extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'feed:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Processes an RSS feed.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$feedId = (int)$this->argument("id");

		$feed = Feed::find($feedId);
		if (!$feed) {
			$this->error(sprintf("Feed ID %d could not be found", $feedId));
			return;
		}

		try {

			$this->info(sprintf("Processing feed %s (ID %d)", $feed->name, $feed->id));

			// Get the live RSS feed
			$rss = new \SimplePie();
			$rss->set_feed_url($feed->uri);
			$rss->enable_cache(false);
			$rss->init();

			// The most recent item.
			$mostRecentLiveItem = $rss->get_item();
			if (!$mostRecentLiveItem) {
				// No entries in this feed, maybe its broken?
				$this->error("No entries for feed");
				return;
			}

			$lastKnownTime = 0;
			$lastItemInDatabase = @json_decode($feed->latest);
			if ($lastItemInDatabase && json_last_error() === JSON_ERROR_NONE) {
				// An item existed before, so we should make sure its not the same as this one.
				if (strtotime($mostRecentLiveItem->get_date()) <= $lastItemInDatabase->timestamp) {
					// Saved item is newer than last item in feed, so don't report.
					$this->error("Parsed item timestamp is the same or older than last known entry. Not processing.");
					return;
				}
				$lastKnownTime = $lastItemInDatabase->timestamp;
			}

			$newItems = [];
			$newItemsTimes = [];

			/* @var $item \SimplePie_Item */
			foreach ($rss->get_items() as $item) {
				$itemTime = strtotime($item->get_date());
				if ($itemTime < $lastKnownTime) {
					continue;
				}
				$newItems[] = [
					"timestamp" => strtotime($item->get_date()),
					"title" => $item->get_title(),
					"content" => $item->get_content(),
					"link" => $item->get_link(),
					"checksum" => md5($item->get_content()),
				];

				$newItemsTimes[] = $itemTime;
			}

			array_multisort($newItemsTimes, $newItems);
			$newItems = array_reverse($newItems);

			if (!empty($newItems)) {
				$lastItemInDatabase = json_encode($newItems[0]);

				// Save last item to database.
				$feed->latest = $lastItemInDatabase;
				$feed->save();
			}

			foreach ($feed->users as $subscriber) {

				$email = View::make('emails.notifications.update')->with([
					"feed" => [
						"name" => $feed->name,
						"description" => $feed->description,
						"items" => $newItems,
					]
				]);

				$dbEmail = Email::create([
					"subject" => "RSS Update",
					"html" => $email->render(),
					"plain" => strip_tags($email->render()),
					"user_id" => $subscriber->id,
					"status" => "queued",
				]);

			}

			Queue::push('mail:send', array('id' => $dbEmail->id));

		} catch (\Exception $e) {
			// So we can do queue stuff again.
		}

		//Queue::later($feed->ttl, 'feed:process', array('id' => $feed->id));
		Queue::later($feed->ttl, 'feed:process', array('id' => $feed->id));


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('id', InputArgument::REQUIRED, 'The feed ID in the database.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}

}
