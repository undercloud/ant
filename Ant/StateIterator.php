<?php

namespace Ant;

use Iterator;
use ArrayIterator;
use EmptyIterator;

/**
 * State iterator
 */
class StateIterator implements \Iterator
{
	private $iterator;
	private $hold;
	private $index = 0;
	private $count = 0;

	/**
	 * Create iterator from given value
	 *
	 * @param mixed $iterable entity
	 */
	public function __construct($iterable = array())
	{
		if ($iterable instanceof Iterator) {
			$this->iterator = $iterable;
		} else if (is_array($iterable)) {
			$this->iterator = new ArrayIterator($iterable);
		} else if (is_object($iterable)) {
			$this->iterator = new ArrayIterator($iterable);
			$this->hold = $iterable;
		} else {
			$this->iterator = new EmptyIterator();
			$this->hold = $iterable;
		}

		$this->count = iterator_count($this->iterator);
	}

	/**
	 * Get current iterator index
	 *
	 * @return integer
	 */
	public function index()
	{
		return $this->index;
	}

	/**
	 * Rewind iterator
	 *
	 * @return void
	 */
	public function rewind()
	{
		$this->index = 0;

		return $this->iterator->rewind();
	}

	/**
	 * Return current iterator value
	 *
	 * @return mixed
	 */
	public function current()
	{
		return $this->iterator->current();
	}

	/**
	 * Return iterator key
	 *
	 * @return mixed
	 */
	public function key()
	{
		return $this->iterator->key();
	}

	/**
	 * Get next iteration
	 *
	 * @return mixed
	 */
	public function next()
	{
		$this->index++;

		return $this->iterator->next();
	}

	/**
	 * Check if iterator is valid
	 *
	 * @return bool
	 */
	public function valid()
	{
		return $this->iterator->valid();
	}

	/**
	 * Check if index is first
	 *
	 * @return bool
	 */
	public function isFirst()
	{
		return (0 === $this->index);
	}

	/**
	 * Check if index is last
	 *
	 * @return bool
	 */
	public function isLast()
	{
		return (1 + $this->index === $this->count);
	}

	/**
	 * Check if index is between first and last
	 *
	 * @return bool
	 */
	public function isMiddle()
	{
		return (!$this->isFirst() and !$this->isLast());
	}

	/**
	 * Check if index is odd
	 *
	 * @return bool
	 */
	public function isOdd()
	{
		return ($this->index % 2 != 0);
	}

	/**
	 * Check if index is even
	 *
	 * @return bool
	 */
	public function isEven()
	{
		return ($this->index % 2 == 0);
	}

	/**
	 * Check if index is divisible by
	 *
	 * @param integer $num parts
	 *
	 * @return bool
	 */
	public function isDivisible($num = 2)
	{
		if ($num <= 0 or !is_numeric($num)) {
			return false;
		}

		return ($this->index % (integer)$num == 0);
	}

	/**
	 * Restore value
	 *
	 * @return mixed
	 */
	public function restore()
	{
		if (null !== $this->hold or $this->iterator instanceof EmptyIterator) {
			return $this->hold;
		} else if ($this->iterator instanceof ArrayIterator) {
			return $this->iterator->getArrayCopy();
		} else {
			return $this->iterator;
		}
	}
}
?>