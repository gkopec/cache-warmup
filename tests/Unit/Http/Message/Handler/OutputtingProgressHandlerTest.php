<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/cache-warmup".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\CacheWarmup\Tests\Unit\Http\Message\Handler;

use EliasHaeussler\CacheWarmup\Http;
use Exception;
use GuzzleHttp\Psr7;
use PHPUnit\Framework;
use Symfony\Component\Console;

/**
 * OutputtingProgressHandlerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class OutputtingProgressHandlerTest extends Framework\TestCase
{
    private Console\Output\BufferedOutput $output;
    private Http\Message\Handler\OutputtingProgressHandler $subject;

    protected function setUp(): void
    {
        $this->output = new Console\Output\BufferedOutput();
        $this->subject = new Http\Message\Handler\OutputtingProgressHandler($this->output, 10);
    }

    /**
     * @test
     */
    public function startProgressBarStartsProgressBar(): void
    {
        $this->subject->startProgressBar();

        self::assertMatchesRegularExpression('#^\s*0/10 \S+\s+0% -- {2}$#m', $this->output->fetch());
    }

    /**
     * @test
     */
    public function finishProgressBarFinishesProgressBar(): void
    {
        $this->subject->startProgressBar();
        $this->subject->finishProgressBar();

        $output = $this->output->fetch();

        self::assertMatchesRegularExpression('#^\s*0/10 \S+\s+0% -- {2}$#m', $output);
        self::assertMatchesRegularExpression('#^\s*10/10 \S+\s+100% -- {2}$#m', $output);
    }

    /**
     * @test
     */
    public function onSuccessPrintsSuccessfulUrlAndAdvancesProgressBarByOneStep(): void
    {
        $response = new Psr7\Response();
        $uri = new Psr7\Uri('https://www.example.com');

        $this->subject->startProgressBar();
        $this->subject->onSuccess($response, $uri);

        self::assertMatchesRegularExpression(
            sprintf('#^\s*1/10 [^\s]+\s+10%% -- %s \(success\)$#m', preg_quote((string) $uri)),
            $this->output->fetch(),
        );
    }

    /**
     * @test
     */
    public function onFailurePrintsFailedUrlAndAdvancesProgressBarByOneStep(): void
    {
        $exception = new Exception('foo');
        $uri = new Psr7\Uri('https://www.example.com');

        $this->subject->startProgressBar();
        $this->subject->onFailure($exception, $uri);

        self::assertMatchesRegularExpression(
            sprintf('#^\s*1/10 [^\s]+\s+10%% -- %s \(failed\)$#m', preg_quote((string) $uri)),
            $this->output->fetch(),
        );
    }
}
