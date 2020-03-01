<?php

namespace MathPHP\Tests\Functions;

use MathPHP\Functions\Piecewise;
use MathPHP\Functions\Polynomial;
use MathPHP\Exception;

class PiecewiseTest extends \PHPUnit\Framework\TestCase
{
    /** @var Piecewise|Mock */
    private $piecewise;

    /**
     * Set up mock Piecewise
     */
    public function setUp()
    {
        $this->piecewise = $this->getMockBuilder(Piecewise::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @testCase     Piecewise __invoke evaluates the expected function to get the expected result
     * @dataProvider dataProviderForEval
     * @param        array $intervals
     * @param        array $polynomial_args
     * @param        array $inputs
     * @param        array $expected
     */
    public function testEval(array $intervals, array $polynomial_args, array $inputs, array $expected)
    {
        if (count($inputs) !== count($expected)) {
            $this->fail('Number of inputs and expected outputs must match');
        }

        $functions = array_map(
            function ($args) {
                return new Polynomial($args);
            },
            $polynomial_args
        );
        $piecewise = new Piecewise($intervals, $functions);

        $n = count($inputs);
        for ($i = 0; $i < $n; $i++) {
            $this->assertEquals($expected[$i], $piecewise($inputs[$i]));
        }
    }

    public function dataProviderForEval(): array
    {
        return [
            // Test evaluation given a single interval, function
            [
                [
                    [-100, 100],  // f interval: [-100, 100]
                ],
                [
                    [1, 0],       // new Polynomial([1, 0])  // f(x) = x
                ],
                [
                    -100, // p(-100) = f(-100) = -100
                    0,    // p(0) = f(0) = 0
                    1,    // p(1) = f(1) = 1
                    25,   // p(25) = f(25) = 25
                    100,  // p(100) = f(100) = 100
                ],
                [
                    -100, // p(-100) = f(-100) = -100
                    0,    // p(0) = f(0) = 0
                    1,    // p(1) = f(1) = 1
                    25,   // p(25) = f(25) = 25
                    100,  // p(100) = f(100) = 100
                ],
            ],
            // Test evaluation in 3 intervals, functions
            [
                [
                    [-100, -2, false, true], // f interval: [-100, -2)
                    [-2, 2],                 // g interval: [-2, 2]
                    [2, 100, true, false]    // h interval: (2, 100]
                ],
                [
                    [-1, 0], // new Polynomial([-1, 0]),      // f(x) = -x
                    [2],     // new Polynomial([2]),          // g(x) = 2
                    [1, 0],  // new Polynomial([1, 0])        // h(x) = x
                ],
                [
                    -27,   // p(-27) = f(-27) = -(-27) = 27
                    -3,    // p(-3) = f(-3) = -(-3) = 3
                    -2,    // p(-2) = g(-2) = 2
                    -1,    // p(-1) = g(-1) = 2
                    0,     // p(0) = g(0) = 2
                    1,     // p(1) = g(1) = 2
                    2,     // p(2) = g(2) = 2
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ],
                [
                    27,    // p(-27) = f(-27) = -(-27) = 27
                    3,     // p(-3) = f(-3) = -(-3) = 3
                    2,     // p(-2) = g(-2) = 2
                    2,     // p(-1) = g(-1) = 2
                    2,     // p(0) = g(0) = 2
                    2,     // p(1) = g(1) = 2
                    2,     // p(2) = g(2) = 2
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ]
            ],
            // Test evaluation of 3 intervals, and at discountinuous, intermediate point
            [
                [
                    [-100, -2, false, true], // f interval: [-100, -2)
                    [-2, 2],                 // g interval: [-2, 2]
                    [2, 100, true, false]    // h interval: (2, 100]
                ],
                [
                    [-1, 0], // new Polynomial([-1, 0]),      // f(x) = -x
                    [100],   // new Polynomial([2]),          // g(x) = 100
                    [1, 0],  // new Polynomial([1, 0])        // h(x) = x
                ],
                [
                    -27,   // p(-27) = f(-27) = -(-27) = 27
                    -3,    // p(-3) = f(-3) = -(-3) = 3
                    -2,    // p(-2) = g(-2) = 100
                    -1,    // p(-1) = g(-1) = 100
                    0,     // p(0) = g(0) = 100
                    1,     // p(1) = g(1) = 100
                    2,     // p(2) = g(2) = 100
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ],
                [
                    27,    // p(-27) = f(-27) = -(-27) = 27
                    3,     // p(-3) = f(-3) = -(-3) = 3
                    100,   // p(-2) = g(-2) = 2
                    100,   // p(-1) = g(-1) = 2
                    100,   // p(0) = g(0) = 2
                    100,   // p(1) = g(1) = 2
                    100,   // p(2) = g(2) = 100
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ]
            ],
            // Test evaluation when intervals are given out of order
            [
                [
                    [-2, 2],                 // g interval: [-2, 2]
                    [-100, -2, false, true], // f interval: [-100, -2)
                    [2, 100, true, false]    // h interval: (2, 100]
                ],
                [
                    [2],     // new Polynomial([2]),          // g(x) = 2
                    [-1, 0], // new Polynomial([-1, 0]),      // f(x) = -x
                    [1, 0],  // new Polynomial([1, 0])        // h(x) = x
                ],
                [
                    -27,   // p(-27) = f(-27) = -(-27) = 27
                    -3,    // p(-3) = f(-3) = -(-3) = 3
                    -2,    // p(-2) = g(-2) = 2
                    -1,    // p(-1) = g(-1) = 2
                    0,     // p(0) = g(0) = 2
                    1,     // p(1) = g(1) = 2
                    2,     // p(2) = g(2) = 2
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ],
                [
                    27,    // p(-27) = f(-27) = -(-27) = 27
                    3,     // p(-3) = f(-3) = -(-3) = 3
                    2,     // p(-2) = g(-2) = 2
                    2,     // p(-1) = g(-1) = 2
                    2,     // p(0) = g(0) = 2
                    2,     // p(1) = g(1) = 2
                    2,     // p(2) = g(2) = 2
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ]
            ],
            // Test evaluation at "jump" located at a single point
            [
                [
                    [-100, -2],           // f interval: [-100, -2]
                    [-2, 2, true, true],  // g interval: (-2, 2)
                    [2, 2],               // z interval: [2, 2]    jump point
                    [2, 100, true, false] // h interval: (2, 100]
                ],
                [
                    [-1, 0], // new Polynomial([-1, 0]),      // f(x) = -x
                    [2],     // new Polynomial([2]),          // g(x) = 2
                    [0],     // new Polynomial([0]),          // z(x) = 0
                    [1, 0],  // new Polynomial([1, 0])        // h(x) = x
                ],
                [
                    -27,   // p(-27) = f(-27) = -(-27) = 27
                    -3,    // p(-3) = f(-3) = -(-3) = 3
                    -2,    // p(-2) = g(-2) = 2
                    -1,    // p(-1) = g(-1) = 2
                    0,     // p(0) = g(0) = 2
                    1,     // p(1) = g(1) = 2
                    2,     // p(2) = z(2) = 0  // jump point
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ],
                [
                    27,    // p(-27) = f(-27) = -(-27) = 27
                    3,     // p(-3) = f(-3) = -(-3) = 3
                    2,     // p(-2) = g(-2) = 2
                    2,     // p(-1) = g(-1) = 2
                    2,     // p(0) = g(0) = 2
                    2,     // p(1) = g(1) = 2
                    0,     // p(2) = z(2) = 0  // jump point
                    3,     // p(3) = h(3) = 3
                    20,    // p(20) = h(20) = 20
                    100,   // p(100) = h(100) = 100
                ]
            ],
            // Large intervals
            [
                [
                    [1499173200, 1499176800, false, true], // f interval: [1499173200, 1499176800)
                    [1499176800, 1499180400],                 // g interval: [1499176800, 1499180400]
                    [1499180400, 1499184000, true, false]    // h interval: (1499180400, 1499184000]
                ],
                [
                    [-1, 0], // new Polynomial([-1, 0]),      // f(x) = -x
                    [2],     // new Polynomial([2]),          // g(x) = 2
                    [1, 0],  // new Polynomial([1, 0])        // h(x) = x
                ],
                [
                    1499173200,   // p(1499173200) = f(1499173200) = -(1499173200) = -1499173200
                    1499173201,   // p(1499173201) = f(1499173201) = -(1499173201) = -1499173201
                    1499176799,   // p(1499176799) = f(1499176799) = -(1499176799) = -1499176799
                    1499176800,   // p(1499176800) = g(1499176800) = 2
                    1499176801,   // p(1499176801) = g(1499176801) = 2
                    1499180400,   // p(1499180400) = g(1499180400) = 2
                    1499180401,   // p(1499180401) = h(1499180401) = 1499180401
                    1499184000,   // p(1499184000) = h(1499184000) = 1499184000
                ],
                [
                    -1499173200,  // p(1499173200) = f(1499173200) = -(1499173200) = -1499173200
                    -1499173201,  // p(1499173201) = f(1499173201) = -(1499173201) = -1499173201
                    -1499176799,  // p(1499176799) = f(1499176799) = -(1499176799) = -1499176799
                    2,            // p(1499176800) = g(1499176800) = 2
                    2,            // p(1499176801) = g(1499176801) = 2
                    2,            // p(1499180400) = g(1499180400) = 2
                    1499180401,   // p(1499180401) = h(1499180401) = 1499180401
                    1499184000,   // p(1499184000) = h(1499184000) = 1499184000
                ]
            ],
        ];
    }

    public function testSubintervalsShareClosedPointException()
    {
        $intervals = [
          [-100, -2],                    // f interval: [-100, -2]
          [-2, 2],                       // g interval: [-2, 2]
          [2, 100]                       // h interval: [2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testSubintervalsOverlapException()
    {
        $intervals = [
          [-100, -2],                    // f interval: [-100, -2]
          [-5, 1],                       // g interval: [-2, 1]
          [2, 100]                       // h interval: [2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testSubintervalDecreasingException()
    {
        $intervals = [
          [-100, -2],                    // f interval: [-100, -2]
          [2, -2, true, true],           // g interval: (-2, 2)
          [2, 100]                       // h interval: [2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testSubintervalContainsMoreThanTwoPoints()
    {
        $intervals = [
          [-100, -2, false, true],      // f interval: [-100, -2)
          [0, 2, 3],                    // g interval: [0, 3]
          [3, 100, true, false]         // h interval: (3, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testSubintervalContainsOnePoints()
    {
        $intervals = [
          [-100, -2, false, true],      // f interval: [-100, -2)
          [-2],                         // g interval: [-2, -2]
          [3, 100, true, false]         // h interval: (3, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testSubintervalContainsOpenPoint()
    {
        $intervals = [
          [-100, -2, false, true],      // f interval: [-100, -2)
          [-2, -2, true, true],         // g interval: (-2, 2)
          [3, 100, true, false]         // h interval: (3, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testInputFunctionsAreNotCallableException()
    {
        $intervals = [
          [-100, -2, false, true],          // f interval: [-100, -2)
          [-2, 2],                          // g interval: [-2, 2]
          [2, 100, true, false]             // h interval: (2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          2,                            // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testNumberOfIntervalsAndFunctionsUnequalException()
    {
        $intervals = [
          [-100, -2, false, true],      // f interval: [-100, -2)
          [0, 2],                       // g interval: [0, 2]
          [2, 100, true, false]         // h interval: (2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    public function testEvaluationNotInDomainException()
    {
        $intervals = [
          [-100, -2, false, true],      // f interval: [-100, -2)
          [0, 2],                       // g interval: [0, 2]
          [2, 100, true, false]         // h interval: (2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
        $evaluation = $piecewise(-1);
    }

    public function testEvaluatedAtOpenPointException()
    {
        $intervals = [
          [-100, -2, true, true],      // f interval: (-100, -2)
          [-2, 2, true, true],         // g interval: (0, 2)
          [2, 100, true, true]         // h interval: (2, 100)
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
        $evaluation = $piecewise(2);
    }

    public function testDuplicatedIntervalException()
    {
        $intervals = [
          [-100, -2, true, true],      // f interval: (-100, -2)
          [-100, -2, true, true],      // g interval: [-100, -2)
          [2, 100]        // h interval: [2, 100]
        ];
        $functions = [
          new Polynomial([-1, 0]),      // f(x) = -x
          new Polynomial([2]),          // g(x) = 2
          new Polynomial([1, 0])        // h(x) = x
        ];

        $this->expectException(Exception\BadDataException::class);
        $piecewise = new Piecewise($intervals, $functions);
    }

    /**
     * @testCase preconditionExceptions throws an Exception\BadDataException if intervals and functions do not have the same number of elements
     */
    public function testConstructorPreconditionCountException()
    {
        $intervals = [
            [1, 2],
            [2, 3],
        ];
        $functions = [
            new Polynomial([2])
        ];

        $preconditions = new \ReflectionMethod(Piecewise::class, 'constructorPreconditions');
        $preconditions->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $preconditions->invokeArgs($this->piecewise, [$intervals, $functions]);
    }

    /**
     * @testCase preconditionExceptions throws an Exception\BadDataException if the functions are not callable
     */
    public function testConstructorPreconditionCallableException()
    {
        $intervals = [
            [1, 2],
            [2, 3],
        ];
        $functions = [
            'not a function',
            'certainly not callable',
        ];

        $preconditions = new \ReflectionMethod(Piecewise::class, 'constructorPreconditions');
        $preconditions->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $preconditions->invokeArgs($this->piecewise, [$intervals, $functions]);
    }

    /**
     * @testCase checkAsAndBs throws an Exception\BadDataException if a point is not closed
     */
    public function testCheckAsAndBsExceptionPointNotClosed()
    {
        list($a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen) = [1, 1, null, null, null, true, true];

        $checkAsAndBs = new \ReflectionMethod(Piecewise::class, 'checkAsAndBs');
        $checkAsAndBs->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $checkAsAndBs->invokeArgs($this->piecewise, [$a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen]);
    }

    /**
     * @testCase checkAsAndBs throws an Exception\BadDataException if interval not increasing
     */
    public function testCheckAsAndBsExceptionIntervalNotIncreasing()
    {
        list($a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen) = [2, 1, null, null, null, true, true];

        $checkAsAndBs = new \ReflectionMethod(Piecewise::class, 'checkAsAndBs');
        $checkAsAndBs->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $checkAsAndBs->invokeArgs($this->piecewise, [$a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen]);
    }

    /**
     * @testCase checkAsAndBs throws an Exception\BadDataException if two intervals share a point that is closed at both ends
     */
    public function testCheckAsAndBsExceptionTwoIntervalsSharePointNotClosedAtBothEnds()
    {
        list($a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen) = [1, 2, null, 1, false, false, true];

        $checkAsAndBs = new \ReflectionMethod(Piecewise::class, 'checkAsAndBs');
        $checkAsAndBs->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $checkAsAndBs->invokeArgs($this->piecewise, [$a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen]);
    }

    /**
     * @testCase checkAsAndBs throws an Exception\BadDataException if one interval starts or ends inside another interval
     */
    public function testCheckAsAndBsExceptionOverlappingIntervals()
    {
        list($a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen) = [3, 4, 2, 4, true, true, true];

        $checkAsAndBs = new \ReflectionMethod(Piecewise::class, 'checkAsAndBs');
        $checkAsAndBs->setAccessible(true);

        $this->expectException(Exception\BadDataException::class);
        $checkAsAndBs->invokeArgs($this->piecewise, [$a, $b, $lastA, $lastB, $lastBOpen, $aOpen, $bOpen]);
    }

    /**
     * @testCase     openOpen interval
     * @dataProvider dataProviderForOpenOpen
     * @param        bool $aOpen
     * @param        bool $bOpen
     * @param        bool $expected
     */
    public function testOpenOpen(bool $aOpen, bool $bOpen, bool $expected)
    {
        $openOpen = new \ReflectionMethod(Piecewise::class, 'openOpen');
        $openOpen->setAccessible(true);

        $this->assertSame($expected, $openOpen->invokeArgs($this->piecewise, [$aOpen, $bOpen]));
    }

    public function dataProviderForOpenOpen(): array
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * @testCase     openClosed interval
     * @dataProvider dataProviderForOpenClosed
     * @param        bool $aOpen
     * @param        bool $bOpen
     * @param        bool $expected
     */
    public function testOpenClosed(bool $aOpen, bool $bOpen, bool $expected)
    {
        $openOpen = new \ReflectionMethod(Piecewise::class, 'openClosed');
        $openOpen->setAccessible(true);

        $this->assertSame($expected, $openOpen->invokeArgs($this->piecewise, [$aOpen, $bOpen]));
    }

    public function dataProviderForOpenClosed(): array
    {
        return [
            [true, true, false],
            [true, false, true],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * @testCase     closedOpen interval
     * @dataProvider dataProviderForClosedOpen
     * @param        bool $aOpen
     * @param        bool $bOpen
     * @param        bool $expected
     */
    public function testClosedOpen(bool $aOpen, bool $bOpen, bool $expected)
    {
        $openOpen = new \ReflectionMethod(Piecewise::class, 'closedOpen');
        $openOpen->setAccessible(true);

        $this->assertSame($expected, $openOpen->invokeArgs($this->piecewise, [$aOpen, $bOpen]));
    }

    public function dataProviderForClosedOpen(): array
    {
        return [
            [true, true, false],
            [true, false, false],
            [false, true, true],
            [false, false, false],
        ];
    }

    /**
     * @testCase     closedClosed interval
     * @dataProvider dataProviderForClosedClosed
     * @param        bool $aOpen
     * @param        bool $bOpen
     * @param        bool $expected
     */
    public function testClosedClosed(bool $aOpen, bool $bOpen, bool $expected)
    {
        $openOpen = new \ReflectionMethod(Piecewise::class, 'closedClosed');
        $openOpen->setAccessible(true);

        $this->assertSame($expected, $openOpen->invokeArgs($this->piecewise, [$aOpen, $bOpen]));
    }

    public function dataProviderForClosedClosed(): array
    {
        return [
            [true, true, false],
            [true, false, false],
            [false, true, false],
            [false, false, true],
        ];
    }
}
