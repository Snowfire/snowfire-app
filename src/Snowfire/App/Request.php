<?php namespace Snowfire\App;

class Request
{
    private $accountId;

    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }
}