<?php


class Email extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'emails';

	protected $fillable = ["subject", "html", "plain","user_id", "status",];

	public function user()
	{
		return $this->belongsTo('User');
	}



}
