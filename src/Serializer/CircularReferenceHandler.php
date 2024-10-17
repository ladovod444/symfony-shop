<?php

namespace App\Serializer;

class CircularReferenceHandler
{
  public function __invoke($object)
  {
    // В случае зацикливания будет возвращаться Id, а не объект
    return $object->getId();
  }
}