<?php
/*
 * Copyright (c) 2013 Evispa Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @author Darius Krištapavičius <darius@evispa.lt>
 */

namespace Evispa\ResourceApiBundle\Manager;

use Evispa\ResourceApiBundle\Backend\FetchParameters;
use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * Class FindResult
 *
 * @package Evispa\ResourceApiBundle\Manager
 */
class FetchResult
{
    /** @var FindParameters */
    private $parameters;

    /** @var ResourceInterface */
    private $resources;

    /** @var Integer */
    private $totalFound;

    /**
     * @param FindParameters $parameters
     * @param ResourceInterface $resources
     * @param $totalFound
     * @internal param int $total
     */
    public function __construct($parameters, $resources, $totalFound)
    {
        $this->parameters = $parameters;
        $this->resources = $resources;
        $this->totalFound = $totalFound;
    }

    /**
     * @return FetchParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return ResourceInterface
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return int
     */
    public function getTotalFound()
    {
        return $this->totalFound;
    }
}
