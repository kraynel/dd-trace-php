<?php
namespace DDTrace\Transport\Jaeger\Thrift;

/**
 * Autogenerated by Thrift Compiler (0.12.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;

final class SpanRefType
{
    const CHILD_OF = 0;

    const FOLLOWS_FROM = 1;

    static public $__names = array(
        0 => 'CHILD_OF',
        1 => 'FOLLOWS_FROM',
    );
}

