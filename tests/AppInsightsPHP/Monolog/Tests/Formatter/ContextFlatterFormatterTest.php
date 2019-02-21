<?php

declare (strict_types=1);

namespace AppInsightsPHP\Monolog\Tests\Formatter;

use AppInsightsPHP\Monolog\Formatter\ContextFlatterFormatter;
use PHPUnit\Framework\TestCase;

final class ContextFlatterFormatterTest extends TestCase
{
    public function test_formatting_record_without_context()
    {
        $formatter = new ContextFlatterFormatter();

        $this->assertEquals(['test' => 'test'], $formatter->format(['test' => 'test']));
    }

    public function test_formatting_multidimensional_array_context_with_scalar_values()
    {
        $formatter = new ContextFlatterFormatter();

        $this->assertEquals(
            [
                'context' => [
                    'string' => 'string',
                    'assoc_array.key.value' => 'value',
                    'scalar_array.0.value' => 'value'
                ]
            ],
            $formatter->format([
                'context' => [
                    'string' => 'string',
                    'assoc_array' => [
                        'key' => [
                            'value' => 'value'
                        ]
                    ],
                    'scalar_array' => [
                        ['value' => 'value']
                    ]
                ]
            ])
        );
    }

    public function test_formatting_multidimensional_array_context_with_mixed_values()
    {
        $formatter = new ContextFlatterFormatter();

        $formatted = $formatter->format([
            'context' => [
                'string' => 'string',
                'assoc_array' => [
                    'exception' => new \Exception('Exception message', 200, new \RuntimeException('Runtime'))
                ],
                'datetime' => new \DateTimeImmutable()
            ]
        ]);

        $this->assertEquals('string', $formatted['context']['string']);
        $this->assertEquals('Exception message', $formatted['context']['assoc_array.exception.message']);
        $this->assertEquals(200, $formatted['context']['assoc_array.exception.code']);
        $this->assertStringStartsWith('[object] (DateTimeImmutable:', $formatted['context']['datetime']);
    }
}