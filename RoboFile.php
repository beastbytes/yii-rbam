<?php

declare(strict_types=1);

use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

class RoboFile extends Tasks
{
    public function prerelease(ConsoleIO $io, string $tag = 'RC'): void
    {
        $result = $this->taskSemVer()
            ->prerelease($tag)
            ->increment('minor')
            ->metadata(date('Ymd\THisT'))
            ->run()
        ;

        $io->say(sprintf(
            'Updated version to %s',
            $result->getMessage()
        ));
    }

    public function release(ConsoleIO $io, string $what): void
    {
        $result = $this->taskSemVer()
            ->increment($what)
            ->metadata(date('Ymd\THisT'))
            ->run()
        ;

        $io->say(sprintf(
            'Updated version to %s',
            $result->getMessage()
        ));
    }

    /**
     * @desc creates a new version tag and pushes to GitHub
     * @param ConsoleIO $io
     * @param string $branch
     */
    public function createTag(ConsoleIO $io, string $branch = 'master'): void
    {
        $tag = (string) $this
            ->taskSemVer()
            ->setFormat('%M.%m.%p')
        ;
        $io->say(sprintf(
            'Creating tag %s on origin::%s',
            $tag,
            $branch
        ));

        $this->taskGitStack()
            ->stopOnFail()
            ->commit('Update version')
            ->push('origin', $branch)
            ->tag($tag)
            ->push('origin', $tag)
            ->run();
    }
}
