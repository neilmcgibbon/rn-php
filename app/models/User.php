<?php


class User extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	public function feeds()
	{
		return $this->belongsToMany('Feed', 'users_feeds', 'user_id', 'feed_id')
			->withTimestamps();
	}

	public function emails() {
		return $this->hasMany('Email');
	}


}
