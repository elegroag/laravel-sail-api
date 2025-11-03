<?php

namespace Tests\Unit;

use App\Library\ProcesadorComandos\ProcesadorComandos;
use Tests\TestCase;

class ProcesadorComandosTest extends TestCase
{
    public function test_runs_command_and_captures_stdout_and_exit_code_zero(): void
    {
        $pc = new ProcesadorComandos('/bin/bash');
        $pc->setAllowedExecutables(['/bin/bash']);
        $pc->setTimeoutSec(5);
        $pc->setLineaComando("-lc 'echo hello'");

        $pc->runnerComando();

        $this->assertSame(0, $pc->getExitCode(), 'Exit code debe ser 0');
        $this->assertStringContainsString('hello', $pc->getOutPutText());
        $this->assertSame('', $pc->getStderr());
    }

    public function test_captures_stderr_and_nonzero_exit_code(): void
    {
        $pc = new ProcesadorComandos('/bin/bash');
        $pc->setAllowedExecutables(['/bin/bash']);
        $pc->setTimeoutSec(5);
        $pc->setLineaComando("-lc 'echo error 1>&2; exit 3'");

        $pc->runnerComando();

        $this->assertSame(3, $pc->getExitCode(), 'Exit code debe ser 3');
        $this->assertStringContainsString('error', $pc->getStderr());
        $this->assertSame('', trim($pc->getOutPutText()));
    }

    public function test_timeout_terminates_process_and_reports_in_stderr(): void
    {
        $pc = new ProcesadorComandos('/bin/bash');
        $pc->setAllowedExecutables(['/bin/bash']);
        $pc->setTimeoutSec(1); // 1 segundo
        $pc->setLineaComando("-lc 'sleep 2; echo after'");

        $pc->runnerComando();

        $this->assertNotSame(0, $pc->getExitCode(), 'Exit code no debe ser 0 en timeout');
        $this->assertStringContainsString('timeout (1s)', $pc->getStderr());
        $this->assertSame('', trim($pc->getOutPutText()));
    }

    public function test_executable_not_in_whitelist_is_blocked(): void
    {
        $pc = new ProcesadorComandos('/bin/bash');
        // whitelist deliberadamente sin bash
        $pc->setAllowedExecutables(['/usr/bin/php']);
        $pc->setTimeoutSec(1);
        $pc->setLineaComando("-lc 'echo hello'");

        $pc->runnerComando();

        $this->assertSame(126, $pc->getExitCode(), 'Debe devolver 126 cuando el ejecutable no está permitido');
        $this->assertStringContainsString('Ejecutable no permitido', $pc->getStderr());
        $this->assertSame('', $pc->getOutPutText());
    }
}
