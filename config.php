<?php

use Ds\Deque;
use Ds\Map;
use Ds\PriorityQueue;
use Ds\Queue;
use Ds\Set;
use Ds\Stack;
use Ds\Vector;

/**
 * Number of times to run through each benchmark to produce an average.
 */
define("TRIALS", 20);

define("SMALL",     [1 <<  9, 1 << 14]);
define("MEDIUM",    [1 << 15, 1 << 20]);
define("LARGE",     [1 << 19, 1 << 24]);

/**
 * The number of samples to take during the benchmark.
 */
define("SAMPLES", 1000);


/**
 * Determines the type of benchmark.
 */
define("INCREMENTAL", 0);
define("EXPONENTIAL", 1);

/**
 *
 */
define("PHP_ARRAY",         "PHP array");
define("VECTOR",            "Vector");
define("SPL_DLL",           "SplDoublyLinkedList");
define("SPL_STACK",         "SplStack");
define("SPL_OS",            "SplObjectStorage");
define("SPL_PQ",            "SplPriorityQueue");
define("DEQUE",             "Deque");
define("QUEUE",             "Queue");
define("SET",               "Set");
define("MAP",               "Map");
define("STACK",             "Stack");
define("PRIORITY_QUEUE",    "PriorityQueue");
define("SPL_FA",            "SplFixedArray");
define("BLOOM",             "BloomFilter");

$a = null; // array or collection

/**
 * Benchmarking task configuration.
 */
return [

    'Stack::pop' => [ INCREMENTAL, [
        SPL_STACK => [
            function($n) { global $a; $a = new SplStack(); for (; $n--; $a[] = rand()); },
            function($i) { global $a; $a->pop(); },
            function()   { global $a; $a = null; },
        ],

        STACK => [
            function($n) { global $a; $a = new Stack(); for (; $n--; $a[] = rand()); },
            function($i) { global $a; $a->pop(); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'PriorityQueue::push' => [ INCREMENTAL , [

        SPL_PQ => [
            function($n) { global $a; $a = new SplPriorityQueue(); },
            function($i) { global $a; $a->insert(rand(), rand());  },
            function()   { global $a; $a = null; },
        ],

        PRIORITY_QUEUE => [
            function($n) { global $a; $a = new PriorityQueue(); },
            function($i) { global $a; $a->push(rand(), rand());  },
            function()   { global $a; $a = null; },
        ],

        QUEUE => [
            function($n) { global $a; $a = new Queue(); },
            function($i) { global $a; $a->push(rand()); rand(); },
            function()   { global $a; $a = null; },
        ]

    ]],

    'Map::put' => [ INCREMENTAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = []; },
            function($i) { global $a; $a[rand(0, $i * 2)] = rand(); },
            function()   { global $a; $a = null; },
        ],

        MAP => [
            function($n) { global $a; $a = new Map(); },
            function($i) { global $a; $a[rand(0, $i * 2)] = rand(); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'Map::remove' => [ INCREMENTAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = []; for (; $n--; $a[$n] = rand()); },
            function($i) { global $a; unset($a[$i]); },
            function()   { global $a; $a = null; },
        ],

        MAP => [
            function($n) { global $a; $a = new Map(); for (; $n--; $a[$n] = rand()); },
            function($i) { global $a; unset($a[$i]); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'Set vs. array_unique' => [EXPONENTIAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = []; for ($i = 0; $i < $n; $i++, $a[] = rand(1, $n / 2)); },
            function($i) { global $a; array_unique($a); },
            function()   { global $a; $a = null; },
        ],

        SET => [
            function($n) { global $a; $a = new Set();  },
            function($i) { global $a; $n = $i; for (; $n--; $a[] = rand(1, $i / 2)); $a->toArray(); },
            function()   { global $a; $a = null; },
        ]
    ]],

    'Set::add' => [ INCREMENTAL, [

        SPL_OS => [
            function($n) { global $a; $a = new SplObjectStorage(); },
            function($i) { global $a; $a->attach(new \stdClass()); },
            function()   { global $a; $a = null; },
        ],

        SET => [
            function($n) { global $a; $a = new Set(); },
            function($i) { global $a; $a[] = new \stdClass(); },
            function()   { global $a; $a = null; },
        ]
    ]],

    'Sequence::unshift' => [ EXPONENTIAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = range(1, $n); },
            function($i) { global $a; array_unshift($a, rand()); },
            function()   { global $a; $a = null; },
        ],

        SPL_DLL => [
            function($n) { global $a; $a = new SplDoublyLinkedList(); for (; $n--; $a[] = rand()); },
            function($i) { global $a; $a->unshift(rand()); },
            function()   { global $a; $a = null; },
        ],

        VECTOR => [
            function($n) { global $a; $a = new Vector(range(1, $n)); },
            function($i) { global $a; $a->unshift(rand()); },
            function()   { global $a; $a = null; },
        ],

        DEQUE => [
            function($n) { global $a; $a = new Deque(range(1, $n)); },
            function($i) { global $a; $a->unshift(rand()); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'Sequence::push (allocated)' => [ INCREMENTAL, [

        SPL_FA => [
            function($n) { global $a; $a = new SplFixedArray($n); },
            function($i) { global $a; $a[$i] = rand(); },
            function()   { global $a; $a = null; },
        ],

        VECTOR => [
            function($n) { global $a; $a = new Vector(); $a->allocate($n); },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        DEQUE => [
            function($n) { global $a; $a = new Deque(); $a->allocate($n); },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        BLOOM => [
            function($n) { global $a; $a = new BloomFilter($n*2, 0.0000001); },
            function($i) { global $a; $a->add(rand()); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'Sequence::push' => [ INCREMENTAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = []; },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        SPL_FA => [
            function($n) { global $a; $a = new SplFixedArray($n); },
            function($i) { global $a; $a[$i] = rand(); },
            function()   { global $a; $a = null; },
        ],

        SPL_DLL => [
            function($n) { global $a; $a = new SplDoublyLinkedList(); },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        VECTOR => [
            function($n) { global $a; $a = new Vector(); },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        DEQUE => [
            function($n) { global $a; $a = new Deque(); },
            function($i) { global $a; $a[] = rand(); },
            function()   { global $a; $a = null; },
        ],

        BLOOM => [
            function($n) { global $a; $a = new BloomFilter($n*2, 0.0000001); },
            function($i) { global $a; $a->add(rand()); },
            function()   { global $a; $a = null; },
        ],
    ]],

    'Sequence::pop' => [ INCREMENTAL, [

        PHP_ARRAY => [
            function($n) { global $a; $a = range(1, $n); },
            function($i) { global $a; array_pop($a); },
            function()   { global $a; $a = null; },
        ],

        SPL_DLL => [
            function($n) { global $a; $a = new SplDoublyLinkedList(); for (; $n--; $a[] = rand()); },
            function($i) { global $a; $a->pop(); },
            function()   { global $a; $a = null; },
        ],

        VECTOR => [
            function($n) { global $a; $a = new Vector(range(1, $n)); },
            function($i) { global $a; $a->pop(); },
            function()   { global $a; $a = null; },
        ],

        DEQUE => [
            function($n) { global $a; $a = new Deque(range(1, $n)); },
            function($i) { global $a; $a->pop(); },
            function()   { global $a; $a = null; },
        ],
    ]],
];
