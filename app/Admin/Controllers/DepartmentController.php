<?php

namespace App\Admin\Controllers;

use App\Models\ClassSTU;
use App\Models\Department;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class DepartmentController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Khoa, lớp');
            $content->description('Danh sách khoa');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Department::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên khoa')->display(function ($name){
                return '<a href="/admin/department/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/department/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at();
            $grid->updated_at();
        });
    }

    protected function gridClass($idClass)
    {
        return Admin::grid(ClassSTU::class, function (Grid $grid) use ($idClass) {
            $grid->model()->whereIn('id', $idClass);
            $grid->id('ID')->sortable();
            $grid->name('Tên lớp');
            $grid->id_department('Tên khoa')->display(function ($idDepartment){
                if($idDepartment) {
                    return Department::find($idDepartment)->name;
                } else {
                    return '';
                }
            });
            $grid->created_at();
            $grid->updated_at();

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();

        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Department::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên khoa');
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $department = Department::findOrFail($id);
            $content->header('Khoa');
            $content->description($department->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id) {
         $form = $this->form()->view($id);
         $idClass = ClassSTU::where('id_department', $id)->pluck('id');
         $gridClass = $this->gridClass($idClass)->render();
         return view('vendor.details',
             [
                 'template_body_name' => 'admin.Department.info',
                 'form' => $form,
                 'gridClass' => $gridClass
             ]
         );

    }
}
