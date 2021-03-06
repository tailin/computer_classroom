<div class="form-group">
    <label for="title">標題*：</label>
    {{ Form::text('title', null, ['id' => 'title', 'class' => 'form-control', 'placeholder' => '請輸入標題','required'=>'required']) }}
</div>

<div class="form-group">
    <label for="content">內文*：</label>
    {{ Form::textarea('content',null, ['id' => 'my-editor', 'class' => 'form-control', 'rows' => 10]) }}
    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
    <script>
        var options = {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
        };
    </script>
    <script>
        CKEDITOR.replace('my-editor', options);
    </script>
</div>