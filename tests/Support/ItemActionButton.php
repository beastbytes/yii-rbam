<?php

namespace Tests\Support;

enum ItemActionButton: int implements ActionButtonInterface
{
    case view = 1;
    case update = 2;
    case remove = 3;
}