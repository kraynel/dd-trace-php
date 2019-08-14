<?php

namespace DDTrace;

use DDTrace\Contracts\Tracer as TracerInterface;

interface Encoder
{
    /**
     * @param TracerInterface $tracer
     * @return object
     */
    public function encodeTraces(TracerInterface $tracer);

    /**
     * @return string
     */
    public function getContentType();
}
