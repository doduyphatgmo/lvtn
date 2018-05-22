<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\Rate;
use App\Models\Semester;

use App\Models\SubjectGroup;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Year;
class SemesterController extends Controller
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

            $content->header('Năm, học kỳ');
            $content->description('Danh sách học kỳ');

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
        return Admin::grid(Semester::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return  '<a href="/admin/semester/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits_max('Số tín chỉ lớn nhất');
            $grid->credits_min('Số tín chỉ nhỏ nhất');
            $grid->id_year('Tên năm')->display(function ($idyear) {
                return Year::find($idyear)->name;
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/semester/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at();
            $grid->updated_at();
        });
    }

    protected function gridSubject($idSemester)
    {
        return Admin::grid(Subjects::class, function (Grid $grid) use ($idSemester) {
            $grid->model()->where('id_semester', $idSemester);
            $grid->id('ID')->sortable();
            $grid->subject_code('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->id_semester('Học kỳ')->display(function ($id) {
                return Semester::find($id)->name;
            });
            $grid->id_subject_group('Nhóm môn')->display(function ($id) {
                return SubjectGroup::find($id)->name;
            });
            $grid->id_rate('Tỷ lệ chuyên cần')->display(function ($rate){
                if($rate){
                    return Rate::find($rate)->attendance;
                } else {
                    return '';
                }
            });
            $grid->column('Tỷ lệ giữa kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->midterm;
                } else {
                    return '';
                }
            });
            $grid->column('Tỷ lệ cuối kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->end_term;
                } else {
                    return '';
                }
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            //disable
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }

    protected function gridSubjectRegister($idSubjects)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->whereIn('id_Subjects', $idSubjects);
            $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->id_classroom('Phòng học')->display(function ($id_classroom){
                if($id_classroom){
                    return Classroom::find($id_classroom)->name;
                } else {
                    return '';
                }
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher){
                if($id_user_teacher){
                    $teacher = UserAdmin::find($id_user_teacher);
                    if($teacher){
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');

            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');

            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Semester::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên học kì');
            $form->select('id_year', 'Năm')->options(Year::all()->pluck('name', 'id'));
            $form->number('credits_max', 'Số tín chỉ lớn nhất');
            $form->number('credits_min', 'Số tín chỉ nhỏ nhất');
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }

    public function details($id){
        return Admin::content(
            function (Content $content) use ($id) {
                $semester = Semester::findOrFail($id);
                $content->header('Học kỳ');
                $content->description($semester->name);
                $content->body($this->detailsView($id));
            });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubject = $this->gridSubject($id)->render();
        $idSubject = Subjects::where('id_semester', $id)->pluck('id');
        $gridSubjectRegister = $this->gridSubjectRegister($idSubject)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Semester.info',
                'form' => $form,
                'gridSubject' => $gridSubject,
                'gridSubjectRegister' => $gridSubjectRegister

            ]
        );
    }
}
