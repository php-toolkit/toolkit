<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/22 0022
 * Time: 12:02
 */

namespace Toolkit\Dev\Console;

use Inhere\Console\Controller;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Toolkit\Sys\Sys;

/**
 * Internal tool for toolkit development
 * @package Toolkit\Dev\Console
 * @author inhere
 */
class DevController extends Controller
{
    const TYPE_SSL   = 'git@github.com:';
    const TYPE_HTTPS = 'https://github.com/';

    protected static $name = 'dev';
    protected static $description = 'Internal tool for toolkit development';

    /**
     * @var string
     * https eg. https://github.com/php-toolkit/php-utils.git
     * ssl eg. git@github.com:php-toolkit/php-utils.git
     */
    public $gitUrl = '%sphp-toolkit/%s.git';

    /** @var array */
    public $components = [];

    /** @var string */
    public $componentDir;

    /**
     * List all swoft component names in the php-toolkit/toolkit
     * @options
     *  --show-repo BOOL       Display remote git repository address.
     * @example
     *  {fullCommand}
     *  {fullCommand} --show-repo
     * @param Input  $input
     * @param Output $output
     * @return int
     */
    public function listCommand(Input $input, Output $output): int
    {
        $this->checkEnv();

        $output->color('Components Total: ' . \count($this->components), 'info');

        $buffer = [];
        $showRepo = (bool)$input->getOpt('show-repo');

        foreach ($this->components as $component) {
            if (!$showRepo) {
                $buffer[] = " $component";
                continue;
            }

            $remote = \sprintf($this->gitUrl, self::TYPE_HTTPS, $component);
            $component = \str_pad($component, 20);
            $buffer[] = \sprintf('  <comment>%s</comment> -  %s', $component, $remote);
        }

        $output->writeln($buffer);

        return 0;
    }

    /**
     * Add component directory code from git repo by 'git subtree add'
     *
     * @usage {fullCommand} [COMPONENTS ...] [--OPTION ...]
     * @arguments
     *  Component[s]   The existing component name[s], allow multi by space.
     * @options
     *  --squash BOOL       Add option '--squash' in git subtree add command. default: <info>True</info>
     *  --dry-run BOOL      Just print all the commands, but do not execute them. default: <info>False</info>
     *  -a, --all BOOL      Pull all components from them's git repo. default: <info>False</info>
     *  -y, --yes BOOL      Do not confirm when execute git subtree push. default: <info>False</info>
     *  --show-result BOOL  Display result for git subtree command exec. default: <info>False</info>
     * @example
     *  {fullCommand} collection         Pull the collection from it's git repo
     *  {fullCommand} collection di      Pull multi component
     * @param Input  $input
     * @param Output $output
     * @return int
     * @throws \RuntimeException
     */
    public function addCommand(Input $input, Output $output): int
    {
        $config = [
            'operate'       => 'add',
            'operatedNames' => 'Will added components',
            'begin'         => 'Execute the add command',
            'doing'         => 'Adding',
            'done'          => "\nOK, A total of 【%s】 components were successfully added"
        ];

        $config['onExec'] = function (string $name) use ($output) {
            $libPath = $this->componentDir . '/libs/' . $name;

            if (\is_dir($libPath)) {
                $output->liteWarning("Component cannot be repeat add: $name");

                return false;
            }

            return true;
        };

        return $this->runGitSubtree($input, $output, $config);
    }

    /**
     * Update component directory code from git repo by 'git subtree pull'
     *
     * @usage {fullCommand} [COMPONENTS ...] [--OPTION ...]
     * @arguments
     *  Component[s]   The existing component name[s], allow multi by space.
     * @options
     *  --squash BOOL       Add option '--squash' in git subtree pull command. default: <info>True</info>
     *  --dry-run BOOL      Just print all the commands, but do not execute them. default: <info>False</info>
     *  -a, --all BOOL      Pull all components from them's git repo. default: <info>False</info>
     *  -y, --yes BOOL      Do not confirm when execute git subtree push. default: <info>False</info>
     *  --show-result BOOL  Display result for git subtree command exec. default: <info>False</info>
     * @example
     *  {fullCommand} collection              Pull the collection from it's git repo
     *  {fullCommand} collection console      Pull multi component
     * @param Input  $input
     * @param Output $output
     * @return int
     * @throws \RuntimeException
     */
    public function pullCommand(Input $input, Output $output): int
    {
        $config = [
            'operate'       => 'pull',
            'operatedNames' => 'Will pulled components',
            'begin'         => 'Execute the pull command',
            'doing'         => 'Pulling',
            'done'          => "\nOK, A total of 【%s】 components were successfully pulled"
        ];

        return $this->runGitSubtree($input, $output, $config);
    }

    /**
     * Push component[s] directory code to component's repo by 'git subtree push'
     *
     * @usage {fullCommand} [COMPONENTS ...] [--OPTION ...]
     * @arguments
     *  Component[s]   The existing component name[s], allow multi by space.
     * @options
     *  --type STRING       Remote git repository address usage protocol. allow: https, ssl. default: <info>https</info>
     *  -a, --all BOOL      Push all components to them's git repo. default: <info>False</info>
     *  -y, --yes BOOL      Do not confirm when execute git subtree push. default: <info>False</info>
     *  --dry-run BOOL      Just print all the commands, but do not execute them. default: <info>False</info>
     *  --squash BOOL       Add option '--squash' in git subtree push command. default: <info>True</info>
     *  --show-result BOOL  Display result for git subtree command exec. default: <info>False</info>
     * @example
     *  {fullCommand} collection              Push the collection to it's git repo
     *  {fullCommand} collection console      Push multi component. collection and console
     *  {fullCommand} --all                Push all components
     *  {fullCommand} --all --dry-run      Push all components, but do not execute.
     * @param Input  $input
     * @param Output $output
     * @return int
     * @throws \RuntimeException
     */
    public function pushCommand(Input $input, Output $output): int
    {
        $config = [
            'operate'       => 'push',
            'operatedNames' => 'Will pushed components',
            'begin'         => 'Execute the push command',
            'doing'         => 'Pushing',
            'done'          => "\nOK, A total of 【%s】 components are pushed to their respective git repositories",
        ];

        return $this->runGitSubtree($input, $output, $config);
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @param array  $config
     * @return int
     * @throws \RuntimeException
     */
    protected function runGitSubtree(Input $input, Output $output, array $config): int
    {
        $this->checkEnv();
        $output->writeln("<comment>Component Directory</comment>:\n $this->componentDir");

        $operate = $config['operate'];
        $names = \array_filter($input->getArgs(), function ($key) {
            return \is_int($key);
        }, ARRAY_FILTER_USE_KEY);

        if ($names) {
            $back = $names;
            $names = \array_intersect($names, $this->components);

            if (!$names) {
                throw new \RuntimeException('Invalid component name entered: ' . \implode(', ', $back));
            }
        } elseif ($input->getSameOpt(['a', 'all'], false)) {
            $names = $this->components;
        } else {
            throw new \RuntimeException('Please enter the name of the component that needs to be operated');
        }

        $output->writeln([
            "<comment>{$config['operatedNames']}</comment>:",
            ' <info>' . \implode(', ', $names) . '</info>'
        ]);

        $doneOne = ' OK';
        $tryRun = (bool)$input->getOpt('dry-run', false);
        $squash = $input->getOpt('squash', true) ? ' --squash' : '';

        $protoType = $input->getOpt('type') ?: 'https';
        $protoHost = $protoType === 'ssl' ? self::TYPE_SSL : self::TYPE_HTTPS;
        $workDir = $this->componentDir;
        $onExec = $config['onExec'] ?? null;

        // eg. git subtree push --prefix=src/view git@github.com:php-toolkit/php-utils.git master [--squash]
        $output->writeln("\n<comment>{$config['begin']}</comment>:");

        foreach ($names as $name) {
            if ($onExec && !$onExec($name)) {
                continue;
            }

            $ret = null;
            $remote = \sprintf($this->gitUrl, $protoHost, $name);
            $command = \sprintf('git subtree %s --prefix=libs/%s %s master%s', $operate, $name, $remote, $squash);

            $output->writeln("> <cyan>$command</cyan>");
            $output->write("{$config['doing']} '$name' ...", false);

            // if '--dry-run' is true. do not exec.
            if (!$tryRun) {
                list($code, $ret, $err) = Sys::run($command, $workDir);

                if ($code !== 0) {
                    throw new \RuntimeException("Exec command failed. command: $command error: $err \nreturn: \n$ret");
                }
            }

            $output->color($doneOne, 'success');

            if ($ret && $input->getOpt('show-result')) {
                $output->writeln(\PHP_EOL . $ret);
            }
        }

        $output->color(\sprintf($config['done'], \count($names)), 'success');

        return 0;
    }

    /**
     * Generate classes API documents by 'sami/sami'
     * @options
     *  --sami STRING       The sami.phar package absolute path.
     *  --force BOOL        The option forces a rebuild docs. default: <info>False</info>
     *  --dry-run BOOL      Just print all the commands, but do not execute them. default: <info>False</info>
     *  --show-result BOOL  Display result for the docs generate. default: <info>False</info>
     * @example
     *  {fullCommand} --sami ~/Workspace/php/tools/sami.phar --force --show-result
     *
     *  About sami:
     *   - An API documentation generator
     *   - github https://github.com/FriendsOfPHP/Sami
     *   - download `curl -O http://get.sensiolabs.org/sami.phar`
     * @param Input  $input
     * @param Output $output
     * @return int
     * @throws \RuntimeException
     */
    public function genApiCommand(Input $input, Output $output): int
    {
        $this->checkEnv();

        $option = '';

        if (!$samiPath = $input->getOpt('sami')) {
            $output->color("Please input the sami.phar path by option '--sami'", 'error');

            return -1;
        }

        if (!\is_file($samiPath)) {
            $output->color('The sami.phar file is not exists! File: ' . $samiPath, 'error');

            return -1;
        }

        $tryRun = (bool)$input->getOpt('dry-run', false);
        $config = $this->componentDir . '/sami.doc.inc';
        $workDir = $this->componentDir;

        if ($input->getOpt('force')) {
            $option .= ' --force';
        }

        // php ~/Workspace/php/tools/sami.phar render --force
        $command = \sprintf(
            'php ~/Workspace/php/tools/sami.phar %s %s%s',
            'update',
            $config,
            $option
        );

        $output->writeln("> <cyan>$command</cyan>");

        // if '--dry-run' is true. do not exec.
        if (!$tryRun) {
            list($code, $ret,) = Sys::run($command, $workDir);

            if ($code !== 0) {
                throw new \RuntimeException("Exec command failed. command: $command return: \n$ret");
            }

            if ($input->getOpt('show-result')) {
                $output->writeln(\PHP_EOL . $ret);
            }
        }

        $output->color("\nOK, Classes reference documents generated!");

        return 0;
    }

    private function checkEnv()
    {
        if (!\defined('TOOLKIT_DIR') || !TOOLKIT_DIR) {
            $this->writeln('<error>Missing the TOOLKIT_DIR define</error>', true);
        }

        $this->componentDir = TOOLKIT_DIR;

        $file = TOOLKIT_DIR . '/components.inc';

        if (!\is_file($file)) {
            $this->writeln('<error>Missing the components config.</error>', true);
        }

        $this->components = include $file;
    }
}

