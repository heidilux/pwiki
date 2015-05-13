@extends('layouts.master')

@section('title')
    @parent
@stop

@section('content')

{!! Form::open() !!}
<div class="col-md-8">
    <textarea id="md-textarea" class="form-control" name="content" rows="20" placeholder="Create your document!"></textarea>
</div>
<button class="btn btn-primary" type="submit">Save</button>
{!! Form::close() !!}



@stop

@section('scripts')
    <script>
        $('textarea').keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                var s = $(this).val();
                $(this).val(s+"\n");
            }
        });​
    </script>
@stop