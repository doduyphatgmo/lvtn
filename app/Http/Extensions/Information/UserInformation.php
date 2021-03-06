<?php

namespace App\Http\Extensions\Information;
//use App\Admin\Extensions\FormLocation\FormLocation;
use Closure;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Navbar;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Encore\Admin\Admin;

/**
 * Class AdminRollOut.
 */
class UserInformation extends Admin
{
    public function form($model, Closure $callable)
    {
        return new FormInformation($this->getModel($model), $callable);
    }

}