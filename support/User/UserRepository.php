<?php

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Support\User;

use BeastBytes\Yii\Rbam\User\UserInterface;
use BeastBytes\Yii\Rbam\User\UserRepositoryInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class UserRepository implements IdentityRepositoryInterface, UserRepositoryInterface
{
    /** @var string[] $users */
    private array $users = [ // UK Prime Ministers to date
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
        'Kier Starmer',
    ];

    public function count(): int
    {
        return count($this->users);
    }

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