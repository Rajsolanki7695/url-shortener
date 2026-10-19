<?php

namespace App\Helpers;

class AdminHelper
{
    public static function me()
    {
        return request()->user();
    }

    public static function myId()
    {
        $user = request()->user();
        return $user ? $user->id : null;
    }

    public static function isSuperadmin()
    {
        $user = request()->user();
        return $user && (int) $user->role_id === 1;
    }

    public static function isView()
    {
        return request()->user() !== null;
    }

    public static function isCreate()
    {
        $user = request()->user();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id !== 1;
    }

    public static function isRead()
    {
        return request()->user() !== null;
    }

    public static function isUpdate()
    {
        $user = request()->user();
        if (!$user) {
            return false;
        }

        return (int) $user->role_id !== 3;
    }

    public static function isDelete()
    {
        $user = request()->user();
        if (!$user) {
            return false;
        }

        return in_array((int) $user->role_id, [1, 2]);
    }

    public static function isCRUD()
    {
        return self::isView() && self::isCreate() && self::isRead() && self::isUpdate() && self::isDelete();
    }

    public static function adminPath($path = '')
    {
        return url('admin/urls/' . $path);
    }
}
