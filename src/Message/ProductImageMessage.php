<?php

namespace App\Message;

class ProductImageMessage
{
  public function __construct(
    private readonly string $content,
  ) {
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public static function create(string $content): self {
    return new self($content);
  }
}