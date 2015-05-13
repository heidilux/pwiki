@extends('layouts.master')

@section('title')
    @parent
    :: {!! $page->title !!}
@stop

@section('content')

    @if (count($links))
        @include('layouts.links')

        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#linkModal">
            <i class="fa fa-pencil"></i> Edit Links
        </button>


        <div class="modal fade" id="linkModal" tabindex="-1" role="dialog" area-labelledby="EditLinks" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="EditLinks">Edit Links</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['route' => 'deleteLinks']) !!}
                        @foreach ($links as $k => $link)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{!! $urls[$k] !!}">
                                    {!! $link['title'] !!}
                                </label>
                            </div>
                        @endforeach
                        <button class="btn btn-danger btn-sm" id="delete-selected">
                            <i class="fa fa-trash"> </i> Delete Selected
                        </button>
                        {!! Form::hidden('path', $filePath) !!}
                        {!! Form::close() !!}
                        <button class="btn btn-success btn-sm new-link-button" type="button" data-toggle="collapse" data-target="#newLink" aria-expanded="false" aria-controls="newLink">
                            <i class="fa fa-plus"> </i> New Link
                        </button>
                    </div>
                    <div class="row">
                        <div class="collapse" id="newLink">
                            <div class="col-sm-6">
                                <div class="well create-link-well">
                                    {!! Form::open(['route' => ['create-link', $filePath]]) !!}
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="link">
                                    </div>
                                    <button type="submit" class="btn btn-success">Create</button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @else
        <button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#newLink" aria-expanded="false" aria-controls="newLink">
            <i class="fa fa-plus"> </i> New Link
        </button>
        <div class="row">
            <div class="collapse" id="newLink">
                <div class="col-sm-6 col-md-3">
                    <div class="well create-link-well">
                        {!! Form::open(['route' => ['create-link', $filePath]]) !!}
                        <div class="form-group">
                            <input type="text" class="form-control" name="link">
                        </div>
                        <button type="submit" class="btn btn-success">Create</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    @endif



    <hr />
    <h2 class="text-center">{!! $page->title !!}</h2>
    {!! $page->content !!}
@stop