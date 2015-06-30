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
        chdir($testRepoPath);

        $git = new Git($testRepoPath);

        $this->assertEquals('master', $git->getCurrentBranch());

        exec('touch README.md && git add README.md && git commit -m "initial commit" && git branch temp && git checkout temp');
        $this->assertEquals('temp', $git->getCurrentBranch());

        unlink('README.md');

        $this->safeDeleteDir($testRepoPath);
    }

    /**
     * @param ...$files
     *
     * @dataProvider filenameProvider
     *
     * @return void
     */
    public function testCommitFiles(...$files)
    {
        $testRepoPath = $this->initGitDirectory('phpunit');
        $this->workDir = $testRepoPath;

        chdir($testRepoPath);

        $git = new Git($testRepoPath);
        foreach ($files as $file) {
            exec('touch ' . $file);
            exec('git add ' . $file);
        }

        $commitFiles = $git->getCommitAffectedFiles();
        $this->assertEquals($files, $commitFiles);

        foreach ($files as $file) {
            unlink($file);
        }

        $this->safeDeleteDir($testRepoPath);

    }

    public function filenameProvider()
    {
        return [
            ['mockFile.txt'],
            ['mockFile2.txt', 'mockFile3.txt'],
        ];
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
        $this->workDir = $repoPath;

        if (!is_dir($repoPath) && !mkdir($repoPath, 0777, true)) {
            throw new RuntimeException('Unable to init git in temp dir: ' . $repoPath);
        }

        if (!chdir($repoPath)) {
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

        if (is_dir($dirName)) {
            exec("rm -rf {$dirName}/.git && rmdir {$dirName}");
        }

    }

    public function __destruct()
    {
        if (is_dir($this->workDir)) {
            exec("rm -rf {$this->workDir}/.git && rm -rf {$this->workDir}");
        }
    }
}