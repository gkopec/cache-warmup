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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\CacheWarmup\Exception;

use EliasHaeussler\CacheWarmup\Crawler;
use RuntimeException;

use function array_pop;
use function count;
use function get_debug_type;
use function implode;
use function sprintf;

/**
 * InvalidCrawlerOptionException.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class InvalidCrawlerOptionException extends RuntimeException
{
    public static function create(Crawler\ConfigurableCrawlerInterface $crawler, string $option): self
    {
        return new self(
            sprintf('The crawler option "%s" is invalid or not supported by crawler "%s".', $option, $crawler::class),
            1659120894,
        );
    }

    /**
     * @param list<string> $options
     */
    public static function createForAll(Crawler\ConfigurableCrawlerInterface $crawler, array $options): self
    {
        if (1 === count($options)) {
            return self::create($crawler, $options[0]);
        }

        $lastOption = array_pop($options);

        return new self(
            sprintf(
                'The crawler options "%s" and "%s" are invalid or not supported by crawler "%s".',
                implode('", "', $options),
                $lastOption,
                $crawler::class,
            ),
            1659206995,
        );
    }

    public static function forInvalidType(mixed $options): self
    {
        return new self(
            sprintf('The crawler options must be an associative array, %s given.', get_debug_type($options)),
            1677424305,
        );
    }
}
