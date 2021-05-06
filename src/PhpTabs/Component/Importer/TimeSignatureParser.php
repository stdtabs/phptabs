<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\TimeSignature;

final class TimeSignatureParser extends ParserBase
{
    /**
     * @var array<string>
     */
    protected $required = ['numerator', 'denominator'];

    /**
     * Parse a time signature array
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->checkKeys($data, $this->required);

        $timeSignature = new TimeSignature();
        $timeSignature->setNumerator($data['numerator']);
        $timeSignature->setDenominator(
            $this->parseDuration($data['denominator'])
        );

        $this->item = $timeSignature;
    }
}
