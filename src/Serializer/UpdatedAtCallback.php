<?php

namespace App\Serializer;

class UpdatedAtCallback
{
  public function __invoke(null|string|\DateTimeInterface $innerObject): null|string|\DateTimeInterface
  {
    if ($innerObject === null) {
      return null;
    }

    if (!($innerObject instanceof \DateTimeInterface)) {
      return $innerObject;
    }

    return $innerObject->format('Y-m-d H:i:s');
    //return 'test UpdatedAt';
  }
}