<?php

namespace DDTrace\Transport\Jaeger;

use DDTrace\Encoder;
use DDTrace\Transport;
use DDTrace\GlobalTracer;
use DDTrace\Configuration;
use DDTrace\Contracts\Tracer;
use DDTrace\Log\LoggingTrait;
use Thrift\Transport\TSocket;
use Thrift\Exception\TException;
use Thrift\Transport\THttpClient;
use Thrift\Protocol\TCompactProtocol;
use DDTrace\Sampling\PrioritySampling;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use DDTrace\Transport\Jaeger\Thrift\Agent\AgentClient;

final class Jaeger implements Transport
{
    use LoggingTrait;

    // Env variables to configure Jaeger agent
    const AGENT_HOST_ENV = 'DD_AGENT_HOST';
    const TRACE_AGENT_PORT_ENV = 'DD_TRACE_AGENT_PORT';

    // Default values for trace agent configuration
    const DEFAULT_AGENT_HOST = 'localhost';
    const DEFAULT_TRACE_AGENT_PORT = '6831';

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var array
     */
    private $config;

    public function __construct(Encoder $encoder, array $config = [])
    {
        $this->configure($config);

        $this->encoder = $encoder;

        $udpTransport = new TUDPTransport($this->config['host'], $this->config['port']);
        $transport = new TBufferedTransport($udpTransport, 1024, 1024);
        $protocol = new TCompactProtocol($transport);
        $this->client = new AgentClient($protocol);
    }

    /**
     * Configures this http transport.
     *
     * @param array $config
     */
    private function configure($config)
    {
        $this->config = array_merge([
            'host' => getenv(self::AGENT_HOST_ENV) ?: self::DEFAULT_AGENT_HOST,
            'port' => getenv(self::TRACE_AGENT_PORT_ENV) ?: self::DEFAULT_TRACE_AGENT_PORT,
        ], $config);
    }

    /**
     * {@inheritdoc}
     */
    public function send(Tracer $tracer)
    {
        $batch = $this->encoder->encodeTraces($tracer);
        self::logDebug('About to send trace(s) to the agent');
        $this->client->emitBatch($batch);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setHeader($key, $value)
    {
        // No header to set
    }
}
