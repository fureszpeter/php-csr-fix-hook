<?php


use GitHook\Git\Git;

class GitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $workDir;

    public function testGetCurrentBranch()
    {
        $testRepoPath = $this->initGitDirectory('phpunit');

        $git = new Git($testRepoPath);
        $branch = $git->getCurrentBranch();

        $this->assertEquals('master', $branch);

        $this->safeDeleteDir($testRepoPath);
    }

    /**
     * @param string $dirName
     * @throws RuntimeException
     *
     * @return string $fullPath
     */
    public function initGitDirectory($dirName)
    {
        $repoPath = sys_get_temp_dir() . '/' . $dirName;

        if (! is_dir($repoPath) && ! mkdir($repoPath, 0777, true)) {
            throw new RuntimeException('Unable to init git in temp dir: ' . $repoPath);
        }

        if (! chdir($repoPath)) {
            throw new RuntimeException('Unable to change workdir to ' . $repoPath);
        }

        exec('git init 2>&1');

        return $repoPath;
    }

    /**
     * @param $dirName
     */
    private function safeDeleteDir($dirName)
    {
        if (is_dir($this->workDir)) {
            exec("rm -rf {$dirName}/.git && rmdir {$dirName}");
        }

    }

    public function __destruct()
    {
        if (is_dir($this->workDir)) {
            exec("rm -rf {$this->workDir}/.git && rmdir {$this->workDir}");
        }
    }
}