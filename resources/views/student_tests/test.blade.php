@extends('layouts.master2')

@section('page-title', '學生測驗|和東資訊教學網')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <h1 class="text-center">{{ $test->title }}</h1>
      <h4 class="text-right">{{ substr(auth()->user()->year_class_num,0,1) }}年{{ substr(auth()->user()->year_class_num,1,2) }}班{{ substr(auth()->user()->year_class_num,3,2) }}號 姓名：{{ auth()->user()->name }}</h4>
      <h2><i class="fa fa-list-ul"></i> 選擇題</h2>
      <h5>(共{{ $num }}題；每題{{ $test->score }}分)</h5>
      {{ Form::open(['route' => 'student_test.store', 'method' => 'POST','name'=>'form1','id'=>'store','onsubmit'=>'return false;']) }}
      <input type="hidden" name="test_id" value="{{ $test->id }}">
      <input type="hidden" name="score" value="{{ $test->score }}">
      <input type="hidden" name="hidden" id="total" value="0">
      @for($i=1;$i<=$num;$i++)
      <div class="card mb-3">
        <div class="card-header">
          <h2 id="t{{ $i }}"><img src="{{ asset('img/red.png') }}">第 ({{ $i }}) 題</h2>
        </div>
        <div class="card-body">
          <?php
            $q = explode('-',session('q'.$i));
          ?>
          <table>
            <tr>
              <td>
                <h3 id="q{{ $i }}">{{ $question_data[$q[1]]['title'] }}</h3>
              </td>
            @if(!empty($question_data[$q[1]]['img_title']))
            <?php $img= str_replace('/','-',$question_data[$q[1]]['img_title']); ?>
              <td>
                <?php list($width,$height,$type,$attr)=getimagesize('../storage/app/'.$question_data[$q[1]]['img_title']); ?>
                  @if($width > 300)
                    <img src="{{ url('question/view_img/'.$img) }}" width="300"></a>
                  @else
                    <img src="{{ url('question/view_img/'.$img) }}"></a>
                  @endif
              </td>
            @endif
            </tr>
          </table>
          <input type="hidden" name="hidden" id="q1{{ $i }}" value="0">
          <table class="table table-hover" id="a{{ $i }}">
            <tr id="a1{{ $i }}" onclick="changecolor1('{{ $i }}');">
              <td>
                <div class="radio">
                  <label>
                    <input type="radio" name="q[{{ $q[1] }}]" id="r1{{ $i }}" value="{{ substr($question_data[$q[1]]['ans_1'],0,1) }}">
                    (1){{ substr($question_data[$q[1]]['ans_1'],2) }}
                  </label>
                </div>
                @if(!empty($question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_1'],0,1)]))
                  <?php $img= str_replace('/','-',$question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_1'],0,1)]); ?>
                  <img src="{{ url('question/view_img/'.$img) }}" width="100"></a>
                @endif
              </td>
            </tr>
            <tr id="a2{{ $i }}" onclick="changecolor2('{{ $i }}');">
              <td>
                <div class="radio">
                  <label>
                    <input type="radio" name="q[{{ $q[1] }}]" id="r2{{ $i }}" value="{{ substr($question_data[$q[1]]['ans_2'],0,1) }}">
                    (2){{ substr($question_data[$q[1]]['ans_2'],2) }}
                  </label>
                </div>
                @if(!empty($question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_2'],0,1)]))
                      <?php $img= str_replace('/','-',$question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_2'],0,1)]); ?>
                  <img src="{{ url('question/view_img/'.$img) }}" width="100"></a>
                @endif
              </td>
            </tr>
            <tr id="a3{{ $i }}" onclick="changecolor3('{{ $i }}');">
              <td>
                <div class="radio">
                  <label>
                    <input type="radio" name="q[{{ $q[1] }}]" id="r3{{ $i }}" value="{{ substr($question_data[$q[1]]['ans_3'],0,1) }}">
                    (3){{ substr($question_data[$q[1]]['ans_3'],2) }}
                  </label>
                </div>
                @if(!empty($question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_3'],0,1)]))
                      <?php $img= str_replace('/','-',$question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_3'],0,1)]); ?>
                  <img src="{{ url('question/view_img/'.$img) }}" width="100"></a>
                @endif
              </td>
            </tr>
            <tr id="a4{{ $i }}" onclick="changecolor4('{{ $i }}');">
              <td>
                <div class="radio">
                  <label>
                    <input type="radio" name="q[{{ $q[1] }}]" id="r4{{ $i }}" value="{{ substr($question_data[$q[1]]['ans_4'],0,1) }}">
                    (4){{ substr($question_data[$q[1]]['ans_4'],2) }}
                  </label>
                </div>
                @if(!empty($question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_4'],0,1)]))
                      <?php $img= str_replace('/','-',$question_data[$q[1]]['img_'.substr($question_data[$q[1]]['ans_4'],0,1)]); ?>
                  <img src="{{ url('question/view_img/'.$img) }}" width="100"></a>
                @endif
              </td>
            </tr>
          </table>
        </div>
      </div>
      @endfor
      <a href="#" id="button" class="btn btn-warning disabled" onclick="bbconfirm('store','確定？')">尚末完成：(<i id="total2">0</i>/{{ $num }})</a>
      {{ Form::close() }}
      <br>
      　
      <SCRIPT>
          function changecolor1(id){
              var total = parseInt(document.getElementById('total').value);
              document.getElementById('t'+id).style.color='silver';
              document.getElementById('q'+id).style.color='silver';
              document.getElementById('a'+id).style.color='silver';

              document.getElementById('r1'+id).checked = true;
              document.getElementById('a1'+id).style.border='0.2cm double red';
              document.getElementById('a2'+id).style.border='';
              document.getElementById('a3'+id).style.border='';
              document.getElementById('a4'+id).style.border='';

              if(document.getElementById('q1'+id).value == 0){
                  document.getElementById('total').value = total+1;
              }
              document.getElementById('q1'+id).value = 1;

              if(document.getElementById('total').value== '{{ $num }}' ){
                  document.getElementById('button').innerText = '已完成，送出答案';
                  document.getElementById('button').className = 'btn btn-success';
              }
              document.getElementById('total2').innerText = document.getElementById('total').value;
          }
          function changecolor2(id){
              var total = parseInt(document.getElementById('total').value);
              document.getElementById('t'+id).style.color='silver';
              document.getElementById('q'+id).style.color='silver';
              document.getElementById('a'+id).style.color='silver';

              document.getElementById('r2'+id).checked = true;
              document.getElementById('a1'+id).style.border='';
              document.getElementById('a2'+id).style.border='0.2cm double red';
              document.getElementById('a3'+id).style.border='';
              document.getElementById('a4'+id).style.border='';

              if(document.getElementById('q1'+id).value == 0){
                  document.getElementById('total').value = total+1;
              }
              document.getElementById('q1'+id).value = 1;

              if(document.getElementById('total').value == '{{ $num }}' ){
                  document.getElementById('button').innerText = '已完成，送出答案';
                  document.getElementById('button').className = 'btn btn-success';
              }
              document.getElementById('total2').innerText = document.getElementById('total').value;
          }
          function changecolor3(id){
              var total = parseInt(document.getElementById('total').value);
              document.getElementById('t'+id).style.color='silver';
              document.getElementById('q'+id).style.color='silver';
              document.getElementById('a'+id).style.color='silver';

              document.getElementById('r3'+id).checked = true;
              document.getElementById('a1'+id).style.border='';
              document.getElementById('a2'+id).style.border='';
              document.getElementById('a3'+id).style.border='0.2cm double red';
              document.getElementById('a4'+id).style.border='';

              if(document.getElementById('q1'+id).value == 0){
                  document.getElementById('total').value = total+1;
              }
              document.getElementById('q1'+id).value = 1;

              if(document.getElementById('total').value == '{{ $num }}' ){
                  document.getElementById('button').innerText = '已完成，送出答案';
                  document.getElementById('button').className = 'btn btn-success';
              }
              document.getElementById('total2').innerText = document.getElementById('total').value;
          }
          function changecolor4(id){
              var total = parseInt(document.getElementById('total').value);
              document.getElementById('t'+id).style.color='silver';
              document.getElementById('q'+id).style.color='silver';
              document.getElementById('a'+id).style.color='silver';

              document.getElementById('r4'+id).checked = true;
              document.getElementById('a1'+id).style.border='';
              document.getElementById('a2'+id).style.border='';
              document.getElementById('a3'+id).style.border='';
              document.getElementById('a4'+id).style.border='0.2cm double red';

              if(document.getElementById('q1'+id).value == 0){
                  document.getElementById('total').value = total+1;
              }
              document.getElementById('q1'+id).value = 1;

              if(document.getElementById('total').value== '{{ $num }}' ){
                  document.getElementById('button').innerText = '已完成，送出答案';
                  document.getElementById('button').className = 'btn btn-success';
              }
              document.getElementById('total2').innerText = document.getElementById('total').value;
          }

          function openwindow(url_str){
              window.open (url_str,"視窗","menubar=0,status=0,directories=0,location=0,top=20,left=20,toolbar=0,scrollbars=1,resizable=1,Width=500,Height=300");
          }


      </SCRIPT>
    </div>
  </div>
</div>
@endsection