<?php namespace Snowfire\App\Repositories;

use Snowfire\App\Storage\AccountStorage;

class AccountsRepository {

    public function first()
    {
        return AccountStorage::
            first();
    }

    public function getById($id)
    {
        return AccountStorage::
            findOrFail($id)->
            first();
    }

    public function getByKey($key)
    {
        return AccountStorage::
            whereAppKey($key)->
            first();
    }

}