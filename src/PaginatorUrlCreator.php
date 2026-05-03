<?php

namespace BeastBytes\Yii\Rbam;

class PaginatorUrlCreator
{
    public function __construct(private readonly string $url)
    {
    }

    public function __invoke(array $arguments, array $queryParameters): string
    {
        $url = $this->url;

        foreach ($arguments as $name => $value) {
            $url .= '/' . $name . '-' . $value;
        }

        if ($queryParameters) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    }
}