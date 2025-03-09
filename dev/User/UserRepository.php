<?php
/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Dev\User;

use BeastBytes\Yii\Rbam\UserInterface;
use BeastBytes\Yii\Rbam\UserRepositoryInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository implements IdentityRepositoryInterface, UserRepositoryInterface
{
    /** @var string[] $users */
    private array $users = [
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
    ];

    /**
     * @return UserInterface[]
     */
    public function findAll(): array
    {
        $users = [];

        foreach ($this->users as $n => $name) {
            $id = $n + 1;
            $users[$n] = new User((string) $id, $name);
        }

        return $users;
    }

    /** @return int[] */
    public function findAllIds(): array
    {
        return range(1, count($this->users));
    }

    public function findById(string $id): UserInterface
    {
        return $this->findIdentity($id);
    }

    /**
     * @param string[] $ids
     * @psalm-param list<non-empty-string> $ids
     * @return array<int, UserInterface>
     */
    public function findByIds(array $ids): array
    {
        $users = [];

        foreach ($ids as $id) {
            $users[] = new User($id, $this->users[(int) $id - 1]);
        }

        return $users;
    }

    /**
     * @param string $id
     * @psalm-param non-empty-string $id
     * @return UserInterface
     */
    public function findIdentity(string $id): UserInterface
    {
        return new User($id, $this->users[(int) $id - 1]);
    }
}