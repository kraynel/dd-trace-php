<?php

namespace DDTrace\Encoders;

use DDTrace\Encoder;
use DDTrace\Configuration;
use DDTrace\Contracts\Tracer;
use DDTrace\Log\LoggingTrait;
use DDTrace\Transport\Jaeger\Thrift\Tag;
use DDTrace\Transport\Jaeger\Thrift\Span;
use DDTrace\Transport\Jaeger\Thrift\Batch;
use DDTrace\Transport\Jaeger\Thrift\Process;
use DDTrace\Transport\Jaeger\Thrift\TagType;

final class Jaeger implements Encoder
{
    use LoggingTrait;

    /**
     * {@inheritdoc}
     */
    public function encodeTraces(Tracer $tracer)
    {
        $spans = [];
        
        $traces = $tracer->getTracesAsArray();
        foreach ($traces as $trace) {
            foreach ($trace as $span) {
                $tags = [];
                foreach (($span['meta'] ?? []) as $tagKey => $tagValue) {
                    $tags[] = new Tag([
                        'key' => $tagKey,
                        'vType' => TagType::STRING,
                        'vStr' => $tagValue,
                    ]);
                }
                $tags[] = new Tag([
                    'key' => 'name',
                    'vType' => TagType::STRING,
                    'vStr' => $span['name'],
                ]);

                $spans[] = new Span([
                    'traceIdLow' => $span['trace_id'],
                    'traceIdHigh' => 0,
                    'spanId' => $span['span_id'],
                    'parentSpanId' => $span['parent_id'] ?? 0,
                    'operationName' => $span['resource'],
                    'flags' => 1,
                    'startTime' => $span['start'] / 1000,
                    'duration' => $span['duration'] / 1000,
                    'tags' => $tags,
                    'logs' => [],
                ]);
            }
        }
        
        return new Batch([
            'process' => new Process([
                'serviceName' => Configuration::get()->appName(),
            ]),
            'spans' => $spans,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return 'application/vnd.apache.thrift.binary';
    }
}
