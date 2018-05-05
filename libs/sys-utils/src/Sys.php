<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/6/27
 * Time: 下午8:18
 */

namespace Toolkit\Sys;

/**
 * Class Sys
 * @package Toolkit\Sys
 */
class Sys extends SysEnv
{
    /**
     * @param string $command
     * @param null|string $logfile
     * @param null|string $user
     * @return mixed
     * @throws \RuntimeException
     */
    public static function exec($command, $logfile = null, $user = null)
    {
        // If should run as another user, we must be on *nix and must have sudo privileges.
        $suDo = '';

        if ($user && self::isUnix() && self::isRoot()) {
            $suDo = "sudo -u $user";
        }

        // Start execution. Run in foreground (will block).
        $logfile = $logfile ?: self::getNullDevice();

        // Start execution. Run in foreground (will block).
        exec("$suDo $command 1>> \"$logfile\" 2>&1", $dummy, $retVal);

        if ($retVal !== 0) {
            throw new \RuntimeException("command exited with status '$retVal'.");
        }

        return $dummy;
    }

    /**
     * run a command. it is support windows
     * @param string $command
     * @param string|null $cwd
     * @return array
     * @throws \RuntimeException
     */
    public static function run(string $command, string $cwd = null): array
    {
        $descriptors = [
            0 => ['pipe', 'r'], // stdin - read channel
            1 => ['pipe', 'w'], // stdout - write channel
            2 => ['pipe', 'w'], // stdout - error channel
            3 => ['pipe', 'r'], // stdin - This is the pipe we can feed the password into
        ];

        $process = \proc_open($command, $descriptors, $pipes, $cwd);

        if (!\is_resource($process)) {
            throw new \RuntimeException("Can't open resource with proc_open.");
        }

        // Nothing to push to input.
        \fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        \fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        \fclose($pipes[2]);

        // TODO: Write passphrase in pipes[3].
        \fclose($pipes[3]);

        // Close all pipes before proc_close! $code === 0 is success.
        $code = \proc_close($process);

        return [$code, $output, $error];
    }

    /**
     * Method to execute a command in the sys
     * Uses :
     * 1. system
     * 2. passthru
     * 3. exec
     * 4. shell_exec
     * @param string $command
     * @param string|null $cwd
     * @param bool $returnStatus
     * @return array|string
     */
    public static function runCommand($command, string $cwd = null, bool $returnStatus = true)
    {
        $exitStatus = 1;

        if ($cwd) {
            \chdir($cwd);
        }

        //system
        if (\function_exists('system')) {
            \ob_start();
            \system($command, $exitStatus);
            $output = \ob_get_contents();
            \ob_end_clean();

            // passthru
        } elseif (\function_exists('passthru')) {
            \ob_start();
            \passthru($command, $exitStatus);
            $output = \ob_get_contents();
            \ob_end_clean();
            //exec
        } else {
            if (\function_exists('exec')) {
                \exec($command, $output, $exitStatus);
                $output = \implode("\n", $output);

                //shell_exec
            } else {
                if (\function_exists('shell_exec')) {
                    $output = \shell_exec($command);
                } else {
                    $output = 'Command execution not possible on this system';
                    $exitStatus = 0;
                }
            }
        }

        if ($returnStatus) {
            return [
                'output' => \trim($output),
                'status' => $exitStatus
            ];
        }

        return \trim($output);
    }

    /**
     * run a command in background
     * @param string $cmd
     */
    public static function bgExec(string $cmd)
    {
        self::execInBackground($cmd);
    }

    /**
     * run a command in background
     * @param string $cmd
     */
    public static function execInBackground(string $cmd)
    {
        if (self::isWindows()) {
            \pclose(\popen('start /B ' . $cmd, 'r'));
        } else {
            \exec($cmd . ' > /dev/null &');
        }
    }

    /**
     * Get unix user of current process.
     * @return array
     */
    public static function getCurrentUser(): array
    {
        return \posix_getpwuid(\posix_getuid());
    }

    /**
     * @return string
     */
    public static function tempDir(): string
    {
        return self::getTempDir();
    }

    /**
     * @return string
     */
    public static function getTempDir(): string
    {
        // @codeCoverageIgnoreStart
        if (\function_exists('sys_get_temp_dir')) {
            $tmp = \sys_get_temp_dir();
        } elseif (!empty($_SERVER['TMP'])) {
            $tmp = $_SERVER['TMP'];
        } elseif (!empty($_SERVER['TEMP'])) {
            $tmp = $_SERVER['TEMP'];
        } elseif (!empty($_SERVER['TMPDIR'])) {
            $tmp = $_SERVER['TMPDIR'];
        } else {
            $tmp = \getcwd();
        }
        // @codeCoverageIgnoreEnd

        return $tmp;
    }

    /**
     * @param string $program
     * @return int|string
     */
    public static function getCpuUsage($program)
    {
        if (!$program) {
            return -1;
        }

        $info = \exec('ps aux | grep ' . $program . ' | grep -v grep | grep -v su | awk {"print $3"}');

        return $info;
    }

    /**
     * @param $program
     * @return int|string
     */
    public static function getMemUsage($program)
    {
        if (!$program) {
            return -1;
        }

        $info = \exec('ps aux | grep ' . $program . ' | grep -v grep | grep -v su | awk {"print $4"}');

        return $info;
    }
}
