<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\User;

use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private array $names = [
        'Robert Walpole',
        'Spencer Compton',
        'Henry Pelham',
        'Thomas Pelham-Holles',
        'William Cavendish',
        'John Stuart',
        'George Grenville',
        'Charles Watson-Wentworth',
        'William Pitt the Elder',
        'Augustus FitzRoy',
        'Frederick North',
        'William Petty',
        'William Cavendish-Bentinck',
        'William Pitt the Younger',
        'Henry Addington',
        'William Grenville',
        'Spencer Perceval',
        'Robert Jenkinson',
        'George Canning',
        'Frederick John Robinson',
        'Arthur Wellesley',
        'Charles Grey',
        'William Lamb',
        'Robert Peel',
        'John Russell',
        'Edward Smith-Stanley',
        'George Hamilton-Gordon',
        'Henry John Temple',
        'Benjamin Disraeli',
        'William Ewart Gladstone',
        'Robert Gascoyne-Cecil',
        'Archibald Primrose',
        'Arthur Balfour',
        'Henry Campbell-Bannerman',
        'H. H. Asquith',
        'David Lloyd George',
        'Bonar Law',
        'Stanley Baldwin',
        'Ramsay MacDonald',
        'Neville Chamberlain',
        'Winston Churchill',
        'Clement Attlee',
        'Anthony Eden',
        'Harold Macmillan',
        'Alec Douglas-Home',
        'Harold Wilson',
        'Edward Heath',
        'James Callaghan',
        'Margaret Thatcher',
        'John Major',
        'Tony Blair',
        'Gordon Brown',
        'David Cameron',
        'Theresa May',
        'Boris Johnson',
        'Liz Truss',
        'Rishi Sunak',
    ];


    private array $users = [];

    public function __construct()
    {
        foreach ($this->names as $id => $name) {
            $id++;
            $this->users[$id] = new User((string) ($id), $name);
        }
    }

    public function findAll(): array
    {
        return $this->users;
    }

    public function findAllIds(): array
    {
        $ids = [];

        foreach ($this->users as $user) {
            $ids[] = $user->getId();
        }

        return $ids;
    }

    public function findByIds(array $ids): array
    {
        $users = [];

        foreach ($ids as $id) {
            $users[] = $this->users[$id];
        }

        return $users;
    }

    public function findById(string $id): UserInterface
    {
        return $this->users[$id];
    }
}
