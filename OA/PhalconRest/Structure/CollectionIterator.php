<?php

namespace OA\PhalconRest\Structure;

class CollectionIterator implements \Iterator
{
   private $var = [];

   public function __construct($array)
   {
       if (is_array($array)) {
           $this->var = $array;
       }
   }

   public function rewind()
   {
       reset($this->var);
   }
 
   public function current()
   {
       return current($this->var);
   }
 
   public function key() 
   {
       return key($this->var);
   }
 
   public function next() 
   {
       return next($this->var);
   }
 
   public function valid()
   {
       $key = key($this->var);
       return ($key !== NULL && $key !== FALSE);
   }
}