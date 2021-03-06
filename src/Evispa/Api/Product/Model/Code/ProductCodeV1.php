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
 * @author Nerijus Arlauskas <nercury@gmail.com>
 */

namespace Evispa\Api\Product\Model\Code;

/**
 * @Evispa\ObjectMigration\Annotations\Version("vnd.evispa.product.code.v1")
 */
class ProductCodeV1
{
    /**
     * Unique product code for a shop.
     *
     * @JMS\Serializer\Annotation\Type("string")
     *
     * @var string $code
     */
    public $code = null;

    /**
     * EAN code.
     *
     * @JMS\Serializer\Annotation\Type("string")
     *
     * @var string $ean
     */
    public $ean = null;

    /**
     * UPC code.
     *
     * @JMS\Serializer\Annotation\Type("string")
     *
     * @var string $upc
     */
    public $upc = null;

    /**
     * @Evispa\ObjectMigration\Annotations\Migration(from="Evispa\Api\Product\Model\Code\CodeV1")
     *
     * @param CodeV1 $other
     * @param $options
     *
     * @return self
     */
    public static function fromCodeV1(CodeV1 $other, $options)
    {
        $code = new self();

        $code->code = $other->code;

        return $code;
    }

    /**
     * @Evispa\ObjectMigration\Annotations\Migration(to="Evispa\Api\Product\Model\Code\CodeV1")
     *
     * @param $options
     * @return CodeV1
     */
    public function toCodeV1($options)
    {
        $other = new CodeV1();

        $other->code = $this->code;

        return $other;
    }
}