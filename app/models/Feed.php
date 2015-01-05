<?php


class Feed extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'feeds';

	public function users()
	{
		return $this->belongsToMany('User', 'users_feeds', 'feed_id', 'user_id')
			->withTimestamps();
	}

}
