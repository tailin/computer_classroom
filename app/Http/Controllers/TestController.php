<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseQuestion;
use App\Group;
use App\StudentTask;
use App\StudMoney;
use App\Test;
use App\TestScore;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = Course::all();
        $course_menu = Course::all()->pluck('name', 'id')->toArray();
        $course_id = ($request->input('course_id'))?$request->input('course_id'):"";
        if(empty($course_id)){
            $num = "";
        }else{
            $num = CourseQuestion::where('course_id','=',$course_id)->count();
        }

        $data = [
            'courses'=>$courses,
            'course_menu'=>$course_menu,
            'course_id'=>$course_id,
            'num'=>$num,
        ];
        return view('admin.tests.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function course_store(Request $request)
    {
        Course::create($request->all());
        return redirect()->route('admin.test.course_index');
    }

    public function course_update(Request $request,Course $course)
    {
        $course->update($request->all());
        return redirect()->route('admin.test.course_index');
    }

    public function course_delete(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.test.course_index');
    }

    public function question_index(Request $request)
    {
        $course_menu = Course::all()->pluck('name', 'id')->toArray();
        $course_id = ($request->input('course_id'))?$request->input('course_id'):"";

        $questions = CourseQuestion::where('course_id','=',$request->input('course_id'))->get();

        $data = [
            'questions'=>$questions,
            'course_menu'=>$course_menu,
            'course_id'=>$course_id,
        ];
        return view('admin.tests.question',$data);
    }


    public function question_store(Request $request)
    {
        $att['course_id'] = $request->input('course_id');
        $att['title'] = $request->input('title');
        $att['ans_A'] = $request->input('ans_A');
        $att['ans_B'] = $request->input('ans_B');
        $att['ans_C'] = $request->input('ans_C');
        $att['ans_D'] = $request->input('ans_D');
        $course_question = CourseQuestion::create($att);

        $files = $request->file('file');
        if(!empty($files)) {
            foreach ($files as $k => $v) {
                $info = [
                    //'mime-type' => $file->getMimeType(),
                    //'original_filename' => $file->getClientOriginalName(),
                    'extension' => $v->getClientOriginalExtension(),
                    //'size' => $file->getClientSize(),
                ];
                $path = "public/questions/" . $course_question->id . "/";
                $filename = $k . "." . $info['extension'];

                $v->storeAs($path, $filename);

                $att2[$k] = $path . $filename;
            }
            $course_question->update($att2);
        }

        return redirect()->route('admin.test.course_index',['course_id'=>$att['course_id']]);

    }

    public function question_update(Request $request,CourseQuestion $course_question)
    {
        $att['title'] = $request->input('title');
        $att['ans_A'] = $request->input('ans_A');
        $att['ans_B'] = $request->input('ans_B');
        $att['ans_C'] = $request->input('ans_C');
        $att['ans_D'] = $request->input('ans_D');
        $course_question ->update($att);

        $files = $request->file('file');
        if(!empty($files)) {
            foreach ($files as $k => $v) {
                $info = [
                    //'mime-type' => $file->getMimeType(),
                    //'original_filename' => $file->getClientOriginalName(),
                    'extension' => $v->getClientOriginalExtension(),
                    //'size' => $file->getClientSize(),
                ];
                $path = "public/questions/" . $course_question->id . "/";
                $filename = $k . "." . $info['extension'];

                $v->storeAs($path, $filename);

                $att2[$k] = $path . $filename;
            }
            $course_question->update($att2);
        }

        return redirect()->route('admin.test.question',['course_id'=>$course_question->course_id]);

    }

    public function question_delete(CourseQuestion $course_question)
    {
        $course_question->delete();
        return redirect()->route('admin.test.question',['course_id'=>$course_question->course_id]);
    }

    public function question_delete_img($img,$id)
    {
        $att[$img] = null;
        $course_question = CourseQuestion::where('id','=',$id)->first();

        if($img == "title_img") $file = '../storage/app/'.$course_question->title_img;
        if($img == "ans_A_img") $file = '../storage/app/'.$course_question->ans_A_img;
        if($img == "ans_B_img") $file = '../storage/app/'.$course_question->ans_B_img;
        if($img == "ans_C_img") $file = '../storage/app/'.$course_question->ans_C_img;
        if($img == "ans_D_img") $file = '../storage/app/'.$course_question->ans_D_img;

        if(file_exists($file)) unlink($file);

        $course_question->update($att);

        return redirect()->route('admin.test.question',['course_id'=>$course_question->course_id]);
    }

    public function question_view_img($img,$id)
    {
        $course_question = CourseQuestion::where('id','=',$id)->first();
        echo "<img src=".url('question/show_img/'.$course_question->id.'/'.$img).">";

    }

    public function test_index(Request $request)
    {
        $course_menu = Course::all()->pluck('name', 'id')->toArray();
        $course_id = ($request->input('course_id'))?$request->input('course_id'):"";
        if(empty($course_id)){
            $groups = [];
            $course_questions = [];
        }else{
            $gs = Group::where('active','=','1')
                ->where('name','like','1%')
                ->get();

            if(!empty($gs)) {
                foreach ($gs as $g) {
                    if (!isset($groups[$g->id])) $groups[$g->id] = null;
                    $groups[$g->id] = $g->name . "(id:" . $g->id . ")";
                }
            }

            $course_questions = CourseQuestion::where('course_id','=',$course_id)
                ->get();
        }

        $tests = Test::all();

        $data = [
            'course_menu'=>$course_menu,
            'course_id'=>$course_id,
            'groups'=>$groups,
            'course_questions'=>$course_questions,
            'tests'=>$tests,
        ];
        return view('admin.tests.test',$data);
    }

    public function test_store(Request $request)
    {
        $att['semester'] = $request->input('semester');
        $att['title'] = $request->input('title');
        $att['score'] = $request->input('score');
        $att['enable'] = $request->input('enable');
        $att['for'] = "";
        $att['questions'] = "";
        //dd($att);
        $for = $request->input('for');
        foreach( $for as $k =>$v){
            $att['for'] .= $v.',';
        }
        $att['for'] = substr($att['for'],0,-1);

        $question = $request->input('question');
        foreach( $question as $k =>$v){
            $att['questions'] .= $v.',';
        }
        $att['questions'] = substr($att['questions'],0,-1);

        Test::create($att);
        return redirect()->route('admin.test_index');
    }

    public function test_delete(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.test_index');
    }

    public function test_update(Test $test)
    {
        if($test->enable == 1){
            $att['enable'] = 0;
        }else{
            $att['enable'] = 1;
        }
        $test->update($att);
        return redirect()->route('admin.test_index');
    }

    public function student_test_index()
    {
        $tests = Test::orderBy('id','DESC')->get();

        $i = 1;
        $get_test = [];

        foreach($tests as $test){
            $group_array = explode(',',$test->for);
            if(auth()->check()) {
                if (in_array(auth()->user()->group_id, $group_array)) {
                    $get_test[$i] = $test;
                    $i++;
                }
            }
        }
        $data = [
            'get_test'=>$get_test,
        ];
        return view('student_tests.index',$data);
    }

    public function student_test_test(Request $request)
    {
        $test = Test::where('id','=',$request->input('test_id'))->first();
        $question_array = explode(',',$test->questions);
        $questions = CourseQuestion::whereIn('id', $question_array)->get();
        foreach($questions as $question){
            $question_data[$question->id]['title'] = $question->title;
            $question_data[$question->id]['ans_1'] = "A"."-".$question->ans_A;
            $question_data[$question->id]['ans_2'] = "B"."-".$question->ans_B;
            $question_data[$question->id]['ans_3'] = "C"."-".$question->ans_C;
            $question_data[$question->id]['ans_4'] = "D"."-".$question->ans_D;
            $question_data[$question->id]['img_title'] = $question->title_img;
            $question_data[$question->id]['img_A'] = $question->ans_A_img;
            $question_data[$question->id]['img_B'] = $question->ans_B_img;
            $question_data[$question->id]['img_C'] = $question->ans_C_img;
            $question_data[$question->id]['img_D'] = $question->ans_D_img;
            for($i=1;$i<=4;$i++){
                $r = rand(1,4);
                $temp = $question_data[$question->id]['ans_'.$i];
                $question_data[$question->id]['ans_'.$i] = $question_data[$question->id]['ans_'.$r];;
                $question_data[$question->id]['ans_'.$r] = $temp;
            }
        }
        $k=1;
        $num = count($question_array);
        foreach($question_array as $key =>$value){
            session(['q'.$k=>$k.'-'.$value]);
            $k++;
        }
        for($i=1;$i<=$num;$i++){
            $r = rand(1,$num);
            $temp = session('q'.$i);
            session(['q'.$i => session('q'.$r)]);
            session(['q'.$r => $temp]);
        }

        $data = [
            'test'=>$test,
            'num'=>$num,
            'question_data'=>$question_data,
        ];
        return view('student_tests.test',$data);
    }

    public function student_test_store(Request $request)
    {
        if(session('score_store') != 1){
            $test = Test::where('id','=',$request->input('test_id'))->first();

            $att['test_id'] = $request->input('test_id');
            $att['user_id'] = auth()->user()->id;
            $att['semester'] = $test->semester;
            $questions = $request->input('q');
            ksort($questions);
            $answers = "";
            $num = 0;
            foreach($questions as $k=>$v){
                $answers .= $v.",";
                if($v == "A") $num++;
            }
            $att['answers'] = substr($answers,0,-1);
            $att['total_score'] = $request->input('score') * $num;

            TestScore::create($att);

            $test = Test::where('id','=',$request->input('test_id'))->first();

            $att2['user_id'] = auth()->user()->id;
            $att2['thing'] = "student_test";
            $att2['thing_id'] = $request->input('test_id');
            $att2['stud_money'] = $request->input('score') * $num;
            $att2['description'] = "測驗「".$test->title."」得分";

            StudMoney::create($att2);
            session(['score_store'=>1]);
        }


        return redirect()->route('student_test.index');
    }

    public function student_test_view(TestScore $test_score)
    {
        $total_score = $test_score->total_score;
        $answer_array = explode(',',$test_score->answers);
        $question_array = explode(',',$test_score->test->questions);
        $test_title = $test_score->test->title;
        $data = [
            'total_score'=>$total_score,
            'answer_array'=>$answer_array,
            'question_array'=>$question_array,
            'test_title'=>$test_title,
        ];
        return view('student_tests.view',$data);
    }

    public function score_index(Request $request)
    {
        $class_array = [];
        $group_id = "";
        $score = [];
        $students = [];

        $test_menu = Test::orderBy('id','DESC')->pluck('title', 'id')->toArray();
        $test_id = (empty($request->input('test_id')))?null:$request->input('test_id');
        if(!empty($test_id)){
            $test_scores = TestScore::where('test_id','=',$test_id)->get();
            foreach($test_scores as $test_score){
                $score[$test_score->user_id] = $test_score->total_score;
            }

            $test = Test::where('id','=',$test_id)->first();
            $class_array = explode(',',$test->for);

            $group_id = (empty($request->input('group_id')))?$class_array[0]:$request->input('group_id');


            $group = Group::where('id','=',$group_id)->first();

            $users = User::where('group_id','=',$group->id)
                ->where('active','=','1')
                ->orderBy('year_class_num')
                ->get();
            foreach($users as $user){
                $students[$user->id]['num'] = substr($user->year_class_num,3,2);
                $students[$user->id]['name'] = $user->name;
                $students[$user->id]['sex'] = $user->sex;
            }

        }


        $data=[
            'test_menu'=>$test_menu,
            'test_id'=>$test_id,
            'group_id'=>$group_id,
            'class_array'=>$class_array,
            'score'=>$score,
            'students'=>$students,
        ];
        return view('admin.tests.score',$data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function score_task()
    {
        $groups = Group::where('active','=','1')->where('id','>',2)->pluck('name', 'id')->toArray();
        return view('admin.tests.score_task',compact('groups'));
    }

    public function score_task_show(Request $request)
    {
        $semester = $request->input('semester');
        $group_id = $request->input('group_id');
        $users = User::where('group_id','=',$group_id)->orderBy('year_class_num')->get();
        $class = Group::where('id','=',$group_id)->first();

        foreach($users as $user){
            $students[$user->id]['num'] = substr($user->year_class_num,3,2);
            $students[$user->id]['name'] = $user->name;
            $students[$user->id]['sex'] = $user->sex;

            $tests = TestScore::where('semester','=',$semester)
                ->where('user_id','=',$user->id)
                ->get();

            $test_num = $tests->count();
            $test_score = 0;

            if($test_num != 0){
                foreach($tests as $test){
                    $test_score += $test->total_score*3;
                }
            }

            $tasks = StudentTask::where('semester','=',$semester)
                ->where('user_id','=',$user->id)
                ->get();

            $task_num = $tasks->count();
            $task_score = 0;
            if($task_num != 0){
                foreach($tasks as $task){
                    $task_score += $task->score;
                }
            }
            if($test_num+$task_num != 0) {
                $students[$user->id]['score'] = ceil(($test_score + $task_score) / ($test_num * 3 + $task_num));
            }else{
                $students[$user->id]['score'] = 0;
            }

        }


        $data = [
            'semester'=>$semester,
            'class_name'=>$class->name,
            'students'=>$students,
        ];

        return view('admin.tests.score_task_show',$data);
    }

    public function getImg($id,$img)
    {
        $course_question = CourseQuestion::where('id','=',$id)->first();
        if($img == "title_img") $file = $course_question->title_img;
        if($img == "ans_A_img") $file = $course_question->ans_A_img;
        if($img == "ans_B_img") $file = $course_question->ans_B_img;
        if($img == "ans_C_img") $file = $course_question->ans_C_img;
        if($img == "ans_D_img") $file = $course_question->ans_D_img;

        $path = storage_path('app/'.$file);
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function viewImg($img)
    {
        $img = str_replace('-','/',$img);
        $path = storage_path('app/'.$img);
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function Img($img)
    {
        $path = storage_path('app/'.$img);
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

}
