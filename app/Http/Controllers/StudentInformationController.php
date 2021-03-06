<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Information\FormInformation;
use App\Http\Extensions\Information\UserInformationFacades;
use App\Models\ClassSTU;
use App\Models\Status;
use App\Models\StudentUser;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Illuminate\Http\Request;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use App\Http\Extensions\src\FormUser;
use Illuminate\Support\MessageBag;

class StudentInformationController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Thông tin cá nhân');
            $content->description('Cá nhân');

            $id = Auth::User()->id;
            $content->body($this->edit($id));
        });
    }

    public function edit($id)
    {
        return User::content(function (ContentUser $content) use ($id) {

            $content->header('Thông tin cá nhân');
            $name = Auth::User()->last_name;
            $content->description($name);
            $content->breadcrumb(
                ['text' => 'Thông tin cá nhân', 'url' => '../user/information']
            );
            $content->body($this->form()->edit($id));
        });
    }
    protected function form()
    {
        return UserInformationFacades::form(StudentUser::class, function (FormInformation $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $form->hidden('id');
            $form->setAction('/user/information/'.$id);
            $form->text('code_number', 'Mã số SV')->default(function ($form) {
                return $form->model()->code_number;
            })->readOnly();
            $form->text('first_name', 'Họ')->rules('required')->default(function ($form) {
                return $form->model()->first_name;
            })->readOnly();
            $form->text('last_name', 'Tên')->rules('required')->default(function ($form) {
                return $form->model()->last_name;
            })->readOnly();
            $form->email('email', 'Email');
            $form->password('password', 'Mật khẩu')->rules('required|confirmed');
            $form->password('password_confirmation', 'Xác nhận mật khẩu')->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
//            $form->image('avatar', 'Ảnh đại diện');
            $form->ignore(['password_confirmation', 'id_class', 'school_year', 'level', 'code_number', 'first_name', 'last_name']);
            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'))->default(function ($form) {
                return $form->model()->id_class;
            })->readOnly();
            $form->year('school_year', 'Năm nhập học')->default(function ($form) {
                return $form->model()->school_year;
            })->readOnly();
            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học'])->default(function ($form) {
                return $form->model()->level;
            })->readOnly();
            $form->disableReset();
            $form->tools(function (Form\Tools $tools) {
                $tools->disableBackButton();
                $tools->disableListButton();
            });
        });
    }
}
