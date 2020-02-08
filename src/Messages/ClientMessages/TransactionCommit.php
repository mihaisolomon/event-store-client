<?php

/**
 * This file is part of `prooph/event-store-client`.
 * (c) 2018-2020 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2018-2020 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Messages\ClientMessages;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStoreClient.Messages.ClientMessages.TransactionCommit</code>
 */
class TransactionCommit extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 transaction_id = 1;</code>
     */
    private int $transaction_id = 0;
    /**
     * Generated from protobuf field <code>bool require_master = 2;</code>
     */
    private bool $require_master = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $transaction_id
     *     @type bool $require_master
     * }
     */
    public function __construct($data = null)
    {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int64 transaction_id = 1;</code>
     * @return int|string
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * Generated from protobuf field <code>int64 transaction_id = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setTransactionId($var)
    {
        GPBUtil::checkInt64($var);
        $this->transaction_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool require_master = 2;</code>
     * @return bool
     */
    public function getRequireMaster()
    {
        return $this->require_master;
    }

    /**
     * Generated from protobuf field <code>bool require_master = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setRequireMaster($var)
    {
        GPBUtil::checkBool($var);
        $this->require_master = $var;

        return $this;
    }
}
