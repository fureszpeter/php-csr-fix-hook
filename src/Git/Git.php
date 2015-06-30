<?php
namespace GitHook\Git;


class Git
{
    /**
     * @return string
     */
    public function getCurrentBranch()
    {
        {
            $output = $this->execute('git symbolic-ref HEAD');

            $tmp = explode('/', $output[0]);

            return $tmp[2];
        }
    }

    /**
     * @param $string
     *
     * @return array
     */
    private function execute($string)
    {
        exec($string, $output);

        return $output;
    }

    /**
     * @return array List of files
     */
    public function getCommitAffectedFiles()
    {
        exec('git diff --cached --name-only --diff-filter=ACM', $output);

        return $output;
    }
}