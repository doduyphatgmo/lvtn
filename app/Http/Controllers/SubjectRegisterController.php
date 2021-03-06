<?php

namespace App\Http\Controllers;

use App\Models\TimeStudy;
use Encore\Admin\Grid\Displayers;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use App\Models\Subjects;
use App\Models\SubjectRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\Year;
use App\Models\SubjectGroup;
use App\Models\Rate;
use App\Models\Classroom;
use App\Models\UserAdmin;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Illuminate\Http\Request;
use Encore\Admin\Form;

use App\Http\Extensions\GridUser;
// use App\Http\Extensions\RowUser;

use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class SubjectRegisterController extends Controller
{
    use ModelForm;

    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');
            $content->breadcrumb(
                ['text' => 'Đăng kí môn học', 'url' => '../user/subject-register']
            );
            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return User::GridUser(Subjects::class, function (GridUser $grid) {
            $grid->registerColumnDisplayer();
            //  $grid->rows(function (RowUser $row) {
            //     $row->column('number', $row->number);
            // });
            // $grid->number('STT');
            $user = Auth::user();

            $schoolYearUser = $user->school_year;
            //check school year
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
//             $schoolYearUser = (string) $schoolYearUser;
            if ($timeRegister) {
//                 //get school year in time register and school year = "ALL"
//                 if(in_array($schoolYearUser, $timeRegister->school_year) || $timeRegister->school_year['0'] == "All") {
//                 } else {
//                     $grid->model()->where('id', -1);
//                 }

                //open subject register follow semester
                $nameSemester = $timeRegister->semester;
                $idSemester = Semester::where('name', $nameSemester)->pluck('id');
                $subjects_id = SemesterSubjects::whereIn('semester_id', $idSemester)->orderBy('semester_id', 'DESC')->pluck('subjects_id')->toArray();
                //sort follow semester
                $field = '';
                foreach ($subjects_id as $id) {
                    $field .= ('"'.$id.'"' . ',');
                }
                $field = substr($field, 0, strlen($field) - 1);
                //get subject user learned
                $idSubjectRegister = ResultRegister::where('id_user_student', $user->id)->where('is_learned', 1)->pluck('id_subject_register')->toArray();
                $idSubjectLearned = SubjectRegister::whereIn('id', $idSubjectRegister)->pluck('id_subjects')->toArray();
                //show subject not learned and subjects in semester in time register (hiển thị các môn chưa học & trong đợt đăng kí đang mở)
                $grid->model()->whereIn('id', $subjects_id)->whereNotIn('id', $idSubjectLearned)->orderBy(DB::raw('FIELD(id, ' . $field . ')'));
            }
            //$grid->id('id');
            $grid->id('Mã môn học')->style("text-align: center;");
            $grid->name('Tên môn học')->display(function ($name) {
                return '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" >' . $name . '</a>';
            });

            $grid->credits('Số tín chỉ')->style("text-align: center;");
            $grid->credits_fee('Số tín chỉ học phí')->style("text-align: center;");
            $grid->column('Nhóm môn')->display(function () {
                $subject = Subjects::find($this->id);
                $nameGroup = $subject->subject_group()->pluck('name')->toArray();
                $groupSubject = array_map(function ($nameGroup){
                    if($nameGroup) {
                        return "<span class='label label-primary'>{$nameGroup}</span>"  ;
                    } else {
                        return '';
                    }
                },$nameGroup);
                return join('&nbsp;', $groupSubject);

            });
            $grid->column('Học kỳ - Năm')->style("text-align: center;")->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map(function ($arraySemester) {
                    $nameSemester = Semester::find($arraySemester)->name;
                    switch ($nameSemester) {
                        case 0 :
                            $nameSemester = 'Học kỳ hè';// học kỳ hè
                            break;
                        case 1:
                            $nameSemester = 'Học kì 1';
                            break;
                        case 2:
                            $nameSemester = 'Học kì 2';
                    }
                    $year = Semester::find($arraySemester)->year()->get()->toArray();
                    if(!empty($year)) {
                        $nameYear = $year['0']['name'];
                         $schoolYear = StudentUser::orderBy('school_year', 'DESC')->groupBy('school_year')->pluck('school_year')->toArray();
                         switch ($nameYear){
                             case 'Năm 1' :
                             $nameYear = 'Khóa '.$schoolYear['0'];
                             break;
                             case 'Năm 2' :
                                 $nameYear = 'Khóa '.$schoolYear['1'];
                                 break;
                             case 'Năm 3' :
                                 $nameYear = 'Khóa '.$schoolYear['2'];
                                 break;
                             case 'Năm 4' :
                                 $nameYear = 'Khóa '.$schoolYear['3'];
                                 break;
                             case 'Năm 5' :
                                 $nameYear = 'Khóa '.$schoolYear['4'];
                                 break;
                             case 'Năm 6' :
                                 $nameYear = 'Khóa '.$schoolYear['5'];
                                 break;
                             default:
                                 $nameYear = '';
                         }

                    } else {
                        $nameYear = '';
                    }
//                    if($nameSemester == 0) {
//                        return "<span class='label label-info'>Học kỳ hè</span>";
//                    }
//                    if(substr($nameYear,4,5) % 2 == 0)
//                    {
//                        return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>";
//                    } else {
//                        return "<span class='label label-success'>{$nameSemester} - {$nameYear}</span>";
//                    }
                    if(substr($nameYear,8,9) % 2 == 0){
                        if($nameSemester == 'Học kỳ hè') {
//                            return  "<span class='label label-primary'>$nameSemester</span>"  ;
                        } else {
                            return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                        }
                    } else {
                        if($nameSemester == 'Học kỳ hè') {
//                            return "<span class='label label-success'>{$nameSemester}</span>";
                        } else {
                            return "<span class='label label-success'>{$nameSemester} - {$nameYear}</span>";
                        }
                    }
                    
                }, $arraySemester);
                    return join('&nbsp;', $name);
            });
            $grid->column('Đăng ký')->style("text-align: center;")->display(function () {
                return '<a href="/user/subject-register/' . $this->id . '/details" data-id='.$this->id.' class="btn btn-md"  target="_blank" ><i class="fa fa-pencil-square-o fa-fw fa-1x"></i></a>';
            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã môn học');
                $filter->like('name', 'Tên môn học');
                $filter->like('credits', 'Tín chỉ');
                $filter->like('credits_fee', 'Tín chỉ học phí');
                $semesters = Semester::all()->toArray();
                $optionSemesters = [];
                foreach($semesters as $semester) {
                    if($semester['name'] == 0) {
                        $optionSemesters += [$semester['id'] => 'Học kỳ hè'];
                    } else {
                        $nameYear = Year::where('id', $semester['id_year'])->first();
                        $nameYear = $nameYear->name;
                        $schoolYear = StudentUser::orderBy('school_year', 'DESC')->groupBy('school_year')->pluck('school_year')->toArray();
                        switch ($nameYear){
                            case 'Năm 1' :
                                $nameYear = 'Khóa '.$schoolYear['0'];
                                break;
                            case 'Năm 2' :
                                $nameYear = 'Khóa '.$schoolYear['1'];
                                break;
                            case 'Năm 3' :
                                $nameYear = 'Khóa '.$schoolYear['2'];
                                break;
                            case 'Năm 4' :
                                $nameYear = 'Khóa '.$schoolYear['3'];
                                break;
                            case 'Năm 5' :
                                $nameYear = 'Khóa '.$schoolYear['4'];
                                break;
                            case 'Năm 6' :
                                $nameYear = 'Khóa '.$schoolYear['5'];
                                break;
                            default:
                                $nameYear = '';
                        }
                        $optionSemesters += [$semester['id'] => 'Học kỳ '. $semester['name']. ' - ' . $nameYear];
                    }
                }
                $filter->where(function ($query){
                    $input = $this->input;
                    $semester = Semester::where('id',$input)->first();
                    $idSubject = $semester->subjects()->pluck('id')->toArray();
                    $query->whereIn('id', $idSubject);
                }, 'Học kì')->select($optionSemesters);
                $filter->where(function ($query){
                    $input = $this->input;
                    $subjectGroup = SubjectGroup::where('id',$input)->first();
                    $idSubject = $subjectGroup->subject()->pluck('id')->toArray();
                    $query->where(function ($query) use ($idSubject) {
                        $query->whereIn('id', $idSubject);
                    });
//                    $query->whereIn('id', $idSubject);
                }, 'Nhóm môn học')->multipleSelect(SubjectGroup::all()->pluck('name', 'id'));
                
//                $filter->in('id_subject1', 'Môn học trước')->multipleSelect(Subjects::all()->pluck('name', 'id'));
//                $filter->in('id_subject2', 'Môn học song song')->multipleSelect(Subjects::all()->pluck('name', 'id'));
               
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->disableActions();
        });
    }
    protected function gridSubjectRegister($idSubjects)
    {
        return User::GridUser(SubjectRegister::class, function (GridUser $grid) use ($idSubjects) {
             $script = <<<SCRIPT
        
    // check subject Before After

            $.ajax({
                method: 'get',
                url: '/user/subject-register/$idSubjects/checkBeforeAtfer',
                data: {
                    _method:'checkBeforeAtfer',
                    _token:LA.token,
                },
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status == false) {
                             swal({
                                  type: 'error',
                                  title:'Thông báo',
                                  text: data.message,
                                 },function() {
                                    window.location.href= ('../../../user/subject-register');
                             });
                        } 
                    }
                }
            });

    //check subject parallel 

            $.ajax({
                method: 'get',
                url: '/user/subject-register/$idSubjects/checkParallel',
                data: {
                    _method:'checkParallel',
                    _token:LA.token,
                },
                success: function (data) {
                    if (typeof data === 'object') {
                        if (data.status == false) {
                             swal({
                                  type: 'error',
                                  title:'Thông báo',
                                  text: data.message,
                                 },function() {
                                    window.location.href= ('../../../user/subject-register');
                             });
                        } 
                    }
                }
            });

SCRIPT;
                    User::script($script);
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
            $grid->model()->where('id_subjects', $idSubjects)->where('id_time_register', $timeRegister->id);
//            $grid->id('ID');
            $grid->id('Mã học phần')->style("text-align: center;");
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->column('Phòng')->style("text-align: center;")->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            });
            $grid->column('Buổi học')->style("text-align: center;")->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
                $day = array_map(function ($day) {
                    switch ($day) {
                        case 2:
                            $day = 'Thứ 2';
                            break;
                        case 3:
                            $day = 'Thứ 3';
                            break;
                        case 4:
                            $day = 'Thứ 4';
                            break;
                        case 5:
                            $day = 'Thứ 5';
                            break;
                        case 6:
                            $day = 'Thứ 6';
                            break;
                        case 7:
                            $day = 'Thứ 7';
                            break;
                        case 8:
                            $day = 'Chủ nhật';
                            break;
                    }

                    return "<span class='label label-success'>{$day}</span>";
                }, $day);
                return join('&nbsp;', $day);
            });
            $grid->column('Thời gian học')->style("text-align: center;")->display(function () {
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                if ($id_user_teacher) {
                    $teacher = UserAdmin::find($id_user_teacher);
                    if ($teacher) {
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại')->style("text-align: center;");
            $grid->qty_max('Số lượng tối đa')->style("text-align: center;");
            $grid->date_start('Ngày bắt đầu')->style("text-align: center;");
            $grid->date_end('Ngày kết thúc')->style("text-align: center;");

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã học phần');
//                $filter->in('id_subjects', 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereIn('id_subjects', $input);
                }, 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->in('id_user_teacher', 'Giảng viên')->multipleSelect(UserAdmin::where('type_user', 0)->pluck('name', 'id'));
                $filter->like('qty_current', 'SL hiện tại');
                $filter->date('date_start', 'Ngày bắt đầu');
                $filter->date('date_end', 'Ngày kết thúc');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->actions(function ($actions) use ($idSubjects){
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-danger btnTotal btnCancel" style="display: none;text-align:center;"><i class="glyphicon glyphicon-trash"></i> &nbsp Hủy bỏ </a>');
//
//                $arrIdSubjectsList=SubjectRegister::where('id_subjects',$idSubjects)->pluck('id')->toArray();
//                $arrIdResultRegister=ResultRegister::get()->pluck('id_subject_register')->toArray();
                $user = Auth::user();
                $idUser = $user->id;
                $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
                $idTimeRegister = $timeRegister->id;
                $idSubjectsList=ResultRegister::where('id_subject',$idSubjects)->where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->first();
//                dd($arrIdSubjectsList);
//                foreach ($arrIdResultRegister as $valueResultRegisters){
//                    foreach ($arrIdSubjectsList as $valueSubjectRegisters){
//                        if($valueResultRegisters==$valueSubjectRegisters)
//                        {
//                            $valueCK = $valueResultRegisters;
                if($idSubjectsList) {
                    $codeSubjectRegister = $idSubjectsList->id_subject_register;
                    $script = <<<SCRIPT
                             $('.btnRegister').each(function(){
                                var idRegister =$(this).data('id');
                                if(idRegister == '$codeSubjectRegister') {
//                                    $('[data-id='+idRegister+']').hide();
                                    $('.btnCancel[data-id='+idRegister+']').css("display", "block");
//                                 $('.btnCancel').find('a[data-id='+idRegister+']').css("display", "block");
                                }
                                else {
                                    $('.btnRegister[data-id='+idRegister+']').css("display", "block");
                                }
                             });
SCRIPT;
                    User::script($script);
                } else {
                    $script = <<<SCRIPT
                                    $('.btnRegister').css("display", "block");

SCRIPT;
                    User::script($script);

                }

//                        }
//                    }
//                }

                //button Register (nút đăng kí)
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-primary btnRegister btnTotal" style="display: none;text-align:center;"  ><i class="fa fa-pencil-square-o"></i> &nbsp Đăng ký </a>');


            });

            $registerConfirm = trans('Bạn có chắc chắn muốn đăng ký không?');
            $confirm = trans('Đăng ký');
            $cancel = trans('Hủy bỏ');
            $cancelConfirm = trans('Bạn có chắc chắn muốn hủy không?');
            $confirmDelete = trans('Hủy đăng ký');
//            $cancel = trans('Hủy bỏ');

            $script = <<<SCRIPT
$('.btnCancel').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$cancelConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dd4b39",
      confirmButtonText: "$confirmDelete",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/delete-register',
            data: {
                _method:'deleteRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                         swal({
                              title: "Hủy thành công", 
                              type: "success"
                             },function() {
                              location.reload();
                             
                         });
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
    });
});






$('.btnRegister').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$registerConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3c8dbc",
      confirmButtonText: "$confirm",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/result-register',
            data: {
                _method:'resultRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                         swal({
                              title: "Đăng ký thành công", 
                              type: "success"
                             },function() {
                              location.reload();
                             
                         });
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
    });
});

SCRIPT;
                User::script($script);
        });
    }

    public function details($id)
    {
        return User::content(function (ContentUser $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);
            $content->breadcrumb(
                ['text' => 'Đăng kí môn học', 'url' => '../user/subject-register'],
                ['text' => $subject->name, 'url' => '../user/subject-register/'.$id.'/deltails']
            );
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id)
    {
//        $form = $this->form()->view($id);
        $gridSubject_Register = $this->gridSubjectRegister($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'User.SubjectRegister.info',
//                'form' => $form,
                'gridSubjectRegister' => $gridSubject_Register

            ]
        );
    }

    public function timetable()
    {

        return view('User.SubjectRegister.timetable');
    }
}
