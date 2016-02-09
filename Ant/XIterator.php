<?php
	namespace Ant;

	use Iterator;
	use ArrayIterator;
	use EmptyIterator;

	class XIterator implements \Iterator
	{
		private $iterator;
		private $hold;
		private $index = 0;
		private $count = 0;

		public function __construct($iterable = array())
		{
			if ($iterable instanceof Iterator) {
				$this->iterator = $iterable;
			} else if (is_array($iterable)) {
				$this->iterator = new ArrayIterator($iterable);
			 } else if(is_object($iterable)) {
				$this->iterator = new ArrayIterator($iterable);
				$this->hold = $iterable;
			} else {
				$this->iterator = new EmptyIterator();
				$this->hold = $iterable;
			}

			$this->count = iterator_count($this->iterator);
		}

		public function rewind() 
		{
			$this->index = 0;

			return $this->iterator->rewind();
		}

		public function current() 
		{
			return $this->iterator->current();
		}

		public function key() 
		{
			return $this->iterator->key();
		}

		public function next() 
		{
			$this->index++;

			return $this->iterator->next();
		}

		public function valid() 
		{
			return $this->iterator->valid();
		}

		public function isFirst()
		{
			return (0 === $this->index);
		}

		public function isLast()
		{
			return (1 + $this->index === $this->count);
		}

		public function isOdd()
		{
			return ($this->index % 2 != 0);
		}

		public function isEven()
		{
			return ($this->index % 2 == 0);
		}

		public function isDivisible($num = 2)
		{
			if ($num <= 0 or !is_numeric($num)) {
				return false;
			}

			return ($this->index % $num == 0);
		}

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