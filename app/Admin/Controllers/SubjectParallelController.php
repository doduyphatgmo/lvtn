<?php

namespace App\Admin\Controllers;

use App\Models\SubjectParallel;

use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class SubjectParallelController extends Controller
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

            $content->header('MH song song');
            $content->description('DS môn song song');

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

            $content->header('Môn học song song');
//            $content->description('description');

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

            $content->header('MH song song');
            $content->description('Thêm môn song song');

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
        return Admin::grid(SubjectParallel::class, function (Grid $grid) {
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id('ID')->sortable();
            $grid->id_subject1('Môn học 1')->display(function ($idSubject1){
                $subject1 = Subjects::find($idSubject1);
                if(!empty($subject1->name)){
                    return '<a href="/admin/subject/' . $idSubject1 . '/details">'.$subject1->name.'</a>';
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_subject2('Môn học 2')->display(function ($idSubject2){
                $subject2 = Subjects::find($idSubject2);
                if(!empty($subject2->name)){
                    return '<a href="/admin/subject/' . $idSubject2 . '/details">'.$subject2->name.'</a>';
                } else {
                    return '';
                }
            })->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
//                $filter->where(function ($query){
//                    $input = $this->input;
//                    $subject1 = Subjects::where('name', 'like' ,'%'.$input.'%' )->pluck('id')->toArray();
//                    $query->where(function ($query) use ($subject1) {
//                        $query->whereIn('id_subject1', $subject1);
//                    });
//                }, 'Môn học trước');
                $filter->in('id_subject1', 'Môn học 1')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->in('id_subject2', 'Môn học 2')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(SubjectParallel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('id_subject1', 'Môn học 1')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_subject2', 'Môn học 2 ')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saving(function (Form $form){
                if($form->id_subject1 == $form->id_subject2 ) {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Môn trước và môn sau không được giống nhau',
                    ]);
                    return back()->with(compact('error'));
                }
            });
            $form->disableReset();
        });
    }
}
