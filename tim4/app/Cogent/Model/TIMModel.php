<?php

namespace Cogent\Model;


use Cogent\Exception\DatabaseSavingException;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Auth;
use InvalidArgumentException;

class TIMModel extends BaseModel 
{

	
	/**
	 * The name of the "last updated by" column.
	 *
	 * @var string
	 */
	const UPDATED_BY = 'LastUpUser';
	
	/**
	 * The name of the "created by" column.
	 *
	 * @var string
	 */
	const CREATED_BY = 'CreatedUser';



	/**
	 * Perform a one-off save that will raise an exception on failure
	 * instead of returning a boolean (which is the default behaviour).
	 *
	 * @return void
	 * @throws \Cogent\Exception\DatabaseSavingException
	 */
	public function saveOrFail()
	{
		$this->updateLastUpdatedBy();
		if (!$this->save())
		{
			throw new DatabaseSavingException(get_class($this) . ' model could not be saved.');
		}
	}

	
	public function updateLastUpdatedBy() {
		if ( ! $this->isDirty(static::UPDATED_BY))
		{
			$this->setUpdatedBy();
		}

		if ( ! $this->exists && ! $this->isDirty(static::CREATED_BY))
		{
			$this->setCreatedBy();
		}
	}
	
		/**
	 * Set the value of the "created at" attribute.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function setCreatedBy()
	{
		$this->{static::CREATED_BY} = Auth::user()->username;
	}

	/**
	 * Set the value of the "updated at" attribute.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function setUpdatedBy()
	{
		$this->{static::UPDATED_BY} = Auth::user()->username;
	}
}