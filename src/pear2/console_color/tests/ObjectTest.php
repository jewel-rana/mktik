<?php

namespace PEAR2\Console\Color;

use PEAR2\Console\Color;
use PEAR2\Console\Color\Backgrounds;
use PEAR2\Console\Color\Fonts;
use PEAR2\Console\Color\Flags;
use PHPUnit_Framework_TestCase;

class ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Color
     */
    public $object;

    public function setUp()
    {
        $this->object = new Color();
    }

    public function setGetFontDataProvider()
    {
        return array(
            0  => array(Fonts::BLACK, "\033[30m"),
            1  => array(Fonts::GREY, "\033[30m"),
            2  => array(Fonts::MAROON, "\033[31m"),
            3  => array(Fonts::RED, "\033[31m"),
            4  => array(Fonts::LIME, "\033[32m"),
            5  => array(Fonts::GREEN, "\033[32m"),
            6  => array(Fonts::BROWN, "\033[33m"),
            7  => array(Fonts::YELLOW, "\033[33m"),
            8  => array(Fonts::NAVY, "\033[34m"),
            9  => array(Fonts::BLUE, "\033[34m"),
            10 => array(Fonts::PURPLE, "\033[35m"),
            11 => array(Fonts::MAGENTA, "\033[35m"),
            12 => array(Fonts::TEAL, "\033[36m"),
            13 => array(Fonts::CYAN, "\033[36m"),
            14 => array(Fonts::SILVER, "\033[37m"),
            15 => array(Fonts::WHITE, "\033[37m"),
            16 => array(Fonts::RESET, "\033[39m"),
        );
    }

    /**
     * Tests getting and setting of fonts.
     * 
     * @param int    $font
     * @param string $sequence
     * 
     * @return void
     * @dataProvider setGetFontDataProvider
     */
    public function testSetGetFont($font, $sequence)
    {
        $this->object->setFont($font);
        $this->assertSame($font, $this->object->getFont());
        $this->assertSame($sequence, (string)$this->object);
    }

    public function setGetBackgroundDataProvider()
    {
        return array(
            0  => array(Backgrounds::BLACK, "\033[40m"),
            1  => array(Backgrounds::GREY, "\033[40m"),
            2  => array(Backgrounds::MAROON, "\033[41m"),
            3  => array(Backgrounds::RED, "\033[41m"),
            4  => array(Backgrounds::GREEN, "\033[42m"),
            5  => array(Backgrounds::LIME, "\033[42m"),
            6  => array(Backgrounds::BROWN, "\033[43m"),
            7  => array(Backgrounds::YELLOW, "\033[43m"),
            8  => array(Backgrounds::NAVY, "\033[44m"),
            9  => array(Backgrounds::BLUE, "\033[44m"),
            10 => array(Backgrounds::PURPLE, "\033[45m"),
            11 => array(Backgrounds::MAGENTA, "\033[45m"),
            12 => array(Backgrounds::TEAL, "\033[46m"),
            13 => array(Backgrounds::CYAN, "\033[46m"),
            14 => array(Backgrounds::SILVER, "\033[47m"),
            15 => array(Backgrounds::WHITE, "\033[47m"),
            16 => array(Backgrounds::RESET, "\033[49m"),
        );
    }

    /**
     * Tests setting and getting of backgrounds.
     * 
     * @param int    $backgorund
     * @param string $sequence
     * 
     * @return void
     * @dataProvider setGetBackgroundDataProvider
     */
    public function testSetGetBackground($backgorund, $sequence)
    {
        $this->object->setBackground($backgorund);
        $this->assertSame($backgorund, $this->object->getBackground());
        $this->assertSame($sequence, (string)$this->object);
    }

    public function setGetFlagsDataProvider()
    {
        return array(
            0 => array(Flags::NONE, Flags::NONE, "\033[m"),
            1 => array(Flags::RESET, Flags::RESET, "\033[0m"),
            2 => array(Flags::INVERSE, Flags::INVERSE, "\033[7m"),
            3 => array(
                Flags::RESET | Flags::INVERSE,
                Flags::RESET | Flags::INVERSE,
                "\033[0;7m"
            ),
            4 => array(
                128 | Flags::RESET | Flags::INVERSE,
                128 | Flags::RESET | Flags::INVERSE,
                "\033[0;7m"
            ),
            5 => array(128, 128, "\033[m"),
            6 => array('a', Flags::NONE, "\033[m"),
            7 => array(true, Flags::RESET, "\033[0m"),
            8 => array('3b', Flags::RESET | Flags::INVERSE, "\033[0;7m"),
            9 => array(101.2, 101, "\033[0m")
        );
    }

    /**
     * Tests setting of flags.
     * 
     * @param mixed  $flags
     * @param int    $expectedFlags
     * @param string $sequence
     * 
     * @return void
     * @dataProvider setGetFlagsDataProvider
     */
    public function testSetGetFlags($flags, $expectedFlags, $sequence)
    {
        $this->object->setFlags($flags);
        $this->assertSame($expectedFlags, $this->object->getFlags());
        $this->assertSame($sequence, (string)$this->object);
    }

    public function setGetStylesDataProvider()
    {
        return array(
            0 => array(0, true, array(), "\033[m"),
            1 => array(0, false, array(), "\033[m"),
            2 => array(
                Styles::ALL,
                true,
                array(
                    Styles::BOLD => true,
                    Styles::UNDERLINE => true,
                    Styles::BLINK => true,
                    Styles::CONCEALED => true
                ),
                "\033[1;4;5;8m"
            ),
            3 => array(
                Styles::ALL,
                false,
                array(
                    Styles::BOLD => false,
                    Styles::UNDERLINE => false,
                    Styles::BLINK => false,
                    Styles::CONCEALED => false
                ),
                "\033[22;24;25;28m"
            ),
            4 => array(
                Styles::BOLD,
                true,
                array(
                    Styles::BOLD => true
                ),
                "\033[1m"
            ),
            5 => array(
                Styles::BOLD | Styles::UNDERLINE,
                true,
                array(
                    Styles::BOLD => true,
                    Styles::UNDERLINE => true
                ),
                "\033[1;4m"
            ),
            6 => array(
                Styles::BOLD | Styles::UNDERLINE,
                false,
                array(
                    Styles::BOLD => false,
                    Styles::UNDERLINE => false
                ),
                "\033[22;24m"
            ),
        );
    }

    /**
     * Tests setting and getting of styles.
     * 
     * @param mixed  $styles
     * @param mixed  $state
     * @param bool[] $states
     * @param string $sequence
     * 
     * @return void
     * @dataProvider setGetStylesDataProvider
     */
    public function testSetGetStyles($styles, $state, $states, $sequence)
    {
        $this->object->setStyles($styles, $state);
        $this->assertSame($states, $this->object->getStyles());
        $this->assertSame($sequence, (string)$this->object);
    }

    public function testSequentialGetSetStyle()
    {
        $this->assertSame(null, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(null, $this->object->getStyles(Styles::UNDERLINE));
        $this->assertSame(null, $this->object->getStyles(Styles::BLINK));
        $this->assertSame(null, $this->object->getStyles(Styles::CONCEALED));

        $this->object->setStyles(Styles::BOLD, true);
        $this->assertSame(true, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(null, $this->object->getStyles(Styles::UNDERLINE));
        $this->assertSame(null, $this->object->getStyles(Styles::BLINK));
        $this->assertSame(null, $this->object->getStyles(Styles::CONCEALED));

        $this->object->setStyles(Styles::BLINK, true);
        $this->assertSame(true, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(null, $this->object->getStyles(Styles::UNDERLINE));
        $this->assertSame(true, $this->object->getStyles(Styles::BLINK));
        $this->assertSame(null, $this->object->getStyles(Styles::CONCEALED));

        $this->object->setStyles(Styles::BOLD | Styles::UNDERLINE, false);
        $this->assertSame(
            array(
                Styles::BOLD => false,
                Styles::UNDERLINE => false,
                Styles::BLINK => true
            ),
            $this->object->getStyles()
        );
        $this->assertSame(false, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(false, $this->object->getStyles(Styles::UNDERLINE));
        $this->assertSame(true, $this->object->getStyles(Styles::BLINK));
        $this->assertSame(null, $this->object->getStyles(Styles::CONCEALED));
    }

    public function testUnsetStyle()
    {
        $this->object->setStyles(Styles::BOLD, true);
        $this->assertSame(true, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(
            array(
                Styles::BOLD => true
            ),
            $this->object->getStyles()
        );

        $this->object->setStyles(Styles::BOLD, null);
        $this->assertSame(null, $this->object->getStyles(Styles::BOLD));
        $this->assertSame(
            array(),
            $this->object->getStyles()
        );
    }

    public function testFluency()
    {
        $this->assertSame(
            $this->object,
            $this->object
                ->setFont(Fonts::BLACK)
                ->setBackground(Backgrounds::BLACK)
                ->setFlags(Flags::RESET)
                ->setStyles(Styles::ALL, true)
        );
    }
}