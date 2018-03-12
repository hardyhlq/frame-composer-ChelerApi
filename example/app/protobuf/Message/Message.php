<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Common.proto

namespace Message;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>message.Message</code>
 */
class Message extends \Google\Protobuf\Internal\Message
{
    /**
     * 消息ID
     *
     * Generated from protobuf field <code>string MessageId = 1;</code>
     */
    private $MessageId = '';
    /**
     * 消息优先级
     *
     * Generated from protobuf field <code>.message.MsgPriority priority = 2;</code>
     */
    private $priority = 0;
    /**
     * 消息选项
     *
     * Generated from protobuf field <code>map<string, string> options = 100;</code>
     */
    private $options;
    /**
     * 消息具体内容
     *
     * Generated from protobuf field <code>bytes Body = 3;</code>
     */
    private $Body = '';

    public function __construct() {
        \GPBMetadata\Common::initOnce();
        parent::__construct();
    }

    /**
     * 消息ID
     *
     * Generated from protobuf field <code>string MessageId = 1;</code>
     * @return string
     */
    public function getMessageId()
    {
        return $this->MessageId;
    }

    /**
     * 消息ID
     *
     * Generated from protobuf field <code>string MessageId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setMessageId($var)
    {
        GPBUtil::checkString($var, True);
        $this->MessageId = $var;

        return $this;
    }

    /**
     * 消息优先级
     *
     * Generated from protobuf field <code>.message.MsgPriority priority = 2;</code>
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * 消息优先级
     *
     * Generated from protobuf field <code>.message.MsgPriority priority = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setPriority($var)
    {
        GPBUtil::checkEnum($var, \Message\MsgPriority::class);
        $this->priority = $var;

        return $this;
    }

    /**
     * 消息选项
     *
     * Generated from protobuf field <code>map<string, string> options = 100;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 消息选项
     *
     * Generated from protobuf field <code>map<string, string> options = 100;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setOptions($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::STRING);
        $this->options = $arr;

        return $this;
    }

    /**
     * 消息具体内容
     *
     * Generated from protobuf field <code>bytes Body = 3;</code>
     * @return string
     */
    public function getBody()
    {
        return $this->Body;
    }

    /**
     * 消息具体内容
     *
     * Generated from protobuf field <code>bytes Body = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setBody($var)
    {
        GPBUtil::checkString($var, False);
        $this->Body = $var;

        return $this;
    }

}
