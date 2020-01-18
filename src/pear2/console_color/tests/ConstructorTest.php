<?php

namespace PEAR2\Console\Color;

use PEAR2\Console\Color;
use PEAR2\Console\Color\Backgrounds;
use PEAR2\Console\Color\Fonts;
use PEAR2\Console\Color\Flags;
use PEAR2\Console\Color\UnexpectedValueException;
use PHPUnit_Framework_TestCase;

class ConstructorTest extends PHPUnit_Framework_TestCase
{
    public function invalidConstructorDataProvider()
    {
        return array(
            0 => array(
                UnexpectedValueException::CODE_FONT,
                99,
                Backgrounds::KEEP
            ),
            1 => array(
                UnexpectedValueException::CODE_FONT,
                99,
                99
            ),
            2 => array(
                UnexpectedValueException::CODE_BACKGROUND,
                Fonts::KEEP,
                99
            )
        );
    }

    /**
     * Tests constructor exceptions.
     * 
     * @param int      $code
     * @param int|null $font
     * @param int|null $background
     * 
     * @return void
     * @dataProvider invalidConstructorDataProvider
     */
    public function testInvalidConstructor($code, $font, $background)
    {
        try {
            new Color($font, $background);
            $this->fail('An exception should have been thrown');
        } catch (UnexpectedValueException $e) {
            $this->assertSame($code, $e->getCode());
        }
    }

    public function constructorDataProvider()
    {
        return array(
            //Flags
            0 => array(
                "\033[m",
                Fonts::KEEP,
                Backgrounds::KEEP,
                Flags::NONE
            ),
            1 => array(
                "\033[0m",
                Fonts::KEEP,
                Backgrounds::KEEP,
                Flags::RESET
            ),
            2 => array(
                "\033[7m",
                Fonts::KEEP,
                Backgrounds::KEEP,
                Flags::INVERSE
            ),
            3 => array(
                "\033[0;7m",
                Fonts::KEEP,
                Backgrounds::KEEP,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with one font
            4 => array(
                "\033[30m",
                Fonts::BLACK,
                Backgrounds::KEEP,
                Flags::NONE
            ),
            5 => array(
                "\033[0;30m",
                Fonts::BLACK,
                Backgrounds::KEEP,
                Flags::RESET
            ),
            6 => array(
                "\033[7;30m",
                Fonts::BLACK,
                Backgrounds::KEEP,
                Flags::INVERSE
            ),
            7 => array(
                "\033[0;7;30m",
                Fonts::BLACK,
                Backgrounds::KEEP,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with another font
            8 => array(
                "\033[31m",
                Fonts::RED,
                Backgrounds::KEEP,
                Flags::NONE
            ),
            9 => array(
                "\033[0;31m",
                Fonts::RED,
                Backgrounds::KEEP,
                Flags::RESET
            ),
            10 => array(
                "\033[7;31m",
                Fonts::RED,
                Backgrounds::KEEP,
                Flags::INVERSE
            ),
            11 => array(
                "\033[0;7;31m",
                Fonts::RED,
                Backgrounds::KEEP,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with one background
            12 => array(
                "\033[40m",
                Fonts::KEEP,
                Backgrounds::BLACK,
                Flags::NONE
            ),
            13 => array(
                "\033[0;40m",
                Fonts::KEEP,
                Backgrounds::BLACK,
                Flags::RESET
            ),
            14 => array(
                "\033[7;40m",
                Fonts::KEEP,
                Backgrounds::BLACK,
                Flags::INVERSE
            ),
            15 => array(
                "\033[0;7;40m",
                Fonts::KEEP,
                Backgrounds::BLACK,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with one font and the same one background
            16 => array(
                "\033[30;40m",
                Fonts::BLACK,
                Backgrounds::BLACK,
                Flags::NONE
            ),
            17 => array(
                "\033[0;30;40m",
                Fonts::BLACK,
                Backgrounds::BLACK,
                Flags::RESET
            ),
            18 => array(
                "\033[7;30;40m",
                Fonts::BLACK,
                Backgrounds::BLACK,
                Flags::INVERSE
            ),
            19 => array(
                "\033[0;7;30;40m",
                Fonts::BLACK,
                Backgrounds::BLACK,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with another font and the same one background
            20 => array(
                "\033[31;40m",
                Fonts::RED,
                Backgrounds::BLACK,
                Flags::NONE
            ),
            21 => array(
                "\033[0;31;40m",
                Fonts::RED,
                Backgrounds::BLACK,
                Flags::RESET
            ),
            22 => array(
                "\033[7;31;40m",
                Fonts::RED,
                Backgrounds::BLACK,
                Flags::INVERSE
            ),
            23 => array(
                "\033[0;7;31;40m",
                Fonts::RED,
                Backgrounds::BLACK,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with another background
            24 => array(
                "\033[41m",
                Fonts::KEEP,
                Backgrounds::RED,
                Flags::NONE
            ),
            25 => array(
                "\033[0;41m",
                Fonts::KEEP,
                Backgrounds::RED,
                Flags::RESET
            ),
            26 => array(
                "\033[7;41m",
                Fonts::KEEP,
                Backgrounds::RED,
                Flags::INVERSE
            ),
            27 => array(
                "\033[0;7;41m",
                Fonts::KEEP,
                Backgrounds::RED,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with one font and the same other background
            28 => array(
                "\033[30;41m",
                Fonts::BLACK,
                Backgrounds::RED,
                Flags::NONE
            ),
            29 => array(
                "\033[0;30;41m",
                Fonts::BLACK,
                Backgrounds::RED,
                Flags::RESET
            ),
            30 => array(
                "\033[7;30;41m",
                Fonts::BLACK,
                Backgrounds::RED,
                Flags::INVERSE
            ),
            31 => array(
                "\033[0;7;30;41m",
                Fonts::BLACK,
                Backgrounds::RED,
                Flags::RESET | Flags::INVERSE
            ),
            //Flags with another font and the same other background
            32 => array(
                "\033[31;41m",
                Fonts::RED,
                Backgrounds::RED,
                Flags::NONE
            ),
            33 => array(
                "\033[0;31;41m",
                Fonts::RED,
                Backgrounds::RED,
                Flags::RESET
            ),
            34 => array(
                "\033[7;31;41m",
                Fonts::RED,
                Backgrounds::RED,
                Flags::INVERSE
            ),
            35 => array(
                "\033[0;7;31;41m",
                Fonts::RED,
                Backgrounds::RED,
                Flags::RESET | Flags::INVERSE
            ),
        );
    }

    /**
     * Tests the constructor.
     * 
     * @param string   $sequence
     * @param int|null $font
     * @param int|null $background
     * @param int      $flags
     * 
     * @return void
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($sequence, $font, $background, $flags)
    {
        $color = new Color($font, $background, $flags);
        $this->assertSame($font, $color->getFont());
        $this->assertSame($background, $color->getBackground());
        $this->assertSame($flags, $color->getFlags());
        $this->assertSame(array(), $color->getStyles());
        $this->assertSame($sequence, (string)$color);
    }

    public function testDefaultConstructor()
    {
        $color = new Color();
        $this->assertSame(Fonts::KEEP, $color->getFont());
        $this->assertSame(Backgrounds::KEEP, $color->getBackground());
        $this->assertSame(Flags::NONE, $color->getFlags());
        $this->assertSame(array(), $color->getStyles());
        $this->assertSame("\033[m", (string)$color);
    }
}