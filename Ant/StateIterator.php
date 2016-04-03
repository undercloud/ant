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
	private $_iterator;
	private $_hold;
	private $_index = 0;
	private $_count = 0;

	/**
	 * Create iterator from given value
	 *
	 * @param mixed $iterable entity
	 */
	public function __construct($iterable = array())
	{
		if ($iterable instanceof Iterator) {
			$this->_iterator = $iterable;
		} else if (is_array($iterable)) {
			$this->_iterator = new ArrayIterator($iterable);
		} else if (is_object($iterable)) {
			$this->_iterator = new ArrayIterator($iterable);
			$this->_hold = $iterable;
		} else {
			$this->_iterator = new EmptyIterator();
			$this->_hold = $iterable;
		}

		$this->_count = iterator_count($this->_iterator);
	}

	/**
	 * Get current iterator index
	 *
	 * @return integer current index
	 */
	public function index()
	{
		return $this->_index;
	}

	/**
	 * Rewind iterator
	 *
	 * @return void
	 */
	public function rewind()
	{
		$this->_index = 0;

		return $this->_iterator->rewind();
	}

	/**
	 * Return current iterator value
	 *
	 * @return mixed
	 */
	public function current()
	{
		return $this->_iterator->current();
	}

	/**
	 * Return iterator key
	 *
	 * @return mixed
	 */
	public function key()
	{
		return $this->_iterator->key();
	}

	/**
	 * Get next iteration
	 *
	 * @return mixed
	 */
	public function next()
	{
		$this->_index++;

		return $this->_iterator->next();
	}

	/**
	 * Check if iterator is valid
	 *
	 * @return bool
	 */
	public function valid()
	{
		return $this->_iterator->valid();
	}

	/**
	 * Check if index is first
	 *
	 * @return bool
	 */
	public function isFirst()
	{
		return (0 === $this->_index);
	}

	/**
	 * Check if index is last
	 *
	 * @return bool
	 */
	public function isLast()
	{
		return (1 + $this->_index === $this->_count);
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
		return ($this->_index % 2 != 0);
	}

	/**
	 * Check if index is even
	 *
	 * @return bool
	 */
	public function isEven()
	{
		return ($this->_index % 2 == 0);
	}

	/**
	 * Check if index is divisible by $num
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

		return ($this->_index % $num == 0);
	}

	/**
	 * Return value given in __constructor
	 *
	 * @return mixed restored value
	 */
	public function restore()
	{
		if (null !== $this->_hold or $this->_iterator instanceof EmptyIterator) {
			return $this->_hold;
		} else if ($this->_iterator instanceof ArrayIterator) {
			return $this->_iterator->getArrayCopy();
		} else {
			return $this->_iterator;
		}
	}
}
?>