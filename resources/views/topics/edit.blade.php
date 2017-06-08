@extends('master')

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-dark">
      <div class="panel-heading"><b>Datos de Tema</b></div>
      <div class="panel-body">
      {!! Form::open(['url' => '/topics/' . $model->id, 'method' => $model->id ? 'PUT' : 'POST']) !!}
        {{csrf_field()}}
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                <input type="text" name="name" class="form-control" placeholder="Nombre de tema" value="{{$model->name}}" />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::textarea('description', $model->description, [
                'class' => 'form-control',
                'placeholder' => 'Descripción'
              ]) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7">
            <button type="submit" class="btn btn-light">
              <i class="fa fa-save"></i>&nbsp;
              Guardar Cambios
            </button>
          @if($model->id)
            <button type="button" class="btn btn-danger" data-id="{{$model->id}}" id="btn-delete">
              <i class="fa fa-trash"></i>&nbsp;
              Eliminar Tema
            </button>
          @endif
            <a href="/dashboard/topics">Volver</a>&nbsp;
          </div>
        </div>
      {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@stop

@if($model->id)
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
  $('#btn-delete').on('click', function(e) {
    if(!confirm('Esta seguro que desea eliminar este elemento?')) {return;}
    var topicId = e.currentTarget.dataset.id;
    $.ajax('/topics/' + topicId, {
      type: 'DELETE',
    }).then(function(res){
      $('#messages').html(' <div class="alert alert-info alert-dismissable">'+
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'+
      '&times;'+
      '</button>'+
      'Tema eliminado exitosamente.'+
      '</div>');
      setTimeout(function() {
        window.location = '/dashboard/topics';
      }, 500);
    }, function(err) {});
    console.log(topicId);
  });
});
</script>
@stop
@endif
