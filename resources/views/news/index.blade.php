@extends('master')

@section('content')
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="panel panel-dark">
      <div class="panel-heading"><b>Lista de Noticias</b></div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <a class="btn btn-light" href="{{url('/dashboard/news/create')}}">
              <i class="fa fa-plus"></i>&nbsp;
              Adicionar Nueva Noticia
            </a>
          </div>
          <div class="col-md-4">
            <a class="btn btn-light" href="{{url('/dashboard/grid/edit')}}">
              <i class="fa fa-pencil"></i>&nbsp;
              Editar Modo Grilla
            </a>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <br />
            {!! Form::open(['method' => 'GET']) !!}
            {{csrf_field()}}
            <input type="hidden" value="yes" name="q" />
            <a data-toggle="collapse" href="#search" aria-expanded="false">
              Búsqueda
            </a>
            <div class="collapse in" id="search">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="fromDate">
                      Desde Fecha
                    </label>
                    <input type="text" name="fromDate" class="form-control datepicker"
                      value="{{$model->fromDate}}" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="toDate">
                      Hasta Fecha
                    </label>
                    <input type="text" name="toDate" class="form-control datepicker"
                      value="{{$model->toDate}}" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="searchBy">Buscar por</label>
                    <select class="form-control" name="searchBy">
                      <option value="published" <?php echo $model->searchBy == 'published' ? 'selected' : '' ?>>
                        Publicada
                      </option>
                      <option value="created" <?php echo $model->searchBy == 'created' ? 'selected' : '' ?>>
                        Ingreso
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="client_id">Cliente</label>
                    {!! Form::select('client_id', $clients, $model->client_id, [
                      'class' => 'form-control'
                    ]) !!}
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="mediaType">Tipo</label>
                    <select name="mediaType" class="form-control">
                      <option value="">--- Seleccione un tipo ---</option>
                      <option value="1" <?php echo $model->mediaType == '1' ? 'selected' : '' ?>> Impreso
                      </option>
                      <option value="2" <?php echo $model->mediaType == '2' ? 'selected' : '' ?>>
                        Digital
                      </option>
                      <option value="3" <?php echo $model->mediaType == '3' ? 'selected' : '' ?>>
                        Radio
                      </option>
                      <option value="4" <?php echo $model->mediaType == '4' ? 'selected' : '' ?>>
                        TV
                      </option>
                      <option value="5" <?php echo $model->mediaType == '5' ? 'selected' : '' ?>>
                        Fuente
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label form="media_id">Medio</label>
                    {!! Form::select('media_id', $media, $model->media_id, [
                      'class' => 'form-control'
                    ]) !!}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="title">Título</label>
                    <input class="form-control" type="text" name="title" value="{{$model->title}}" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="tendency">Tendencia</label>
                    <select name="tendency" class="form-control">
                      <option value="">--- Seleccione tendencia ---</option>
                      <option value="1" <?php echo $model->tendency == '1' ? 'selected' : '' ?>>
                        Positiva
                      </option>
                      <option value="2" <?php echo $model->tendency == '2' ? 'selected' : '' ?>>
                        Negativa
                      </option>
                      <option value="3" <?php echo $model->tendency == '3' ? 'selected' : '' ?>>
                        Neutra
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="source">Fuente</label>
                    <input type="text" name="source" class="form-control" value="{{$model->source}}" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="show">Programa</label>
                    <input type="text" name="show" class="form-control" value="{{$model->show}}" />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="gender">Género</label>
                    <input type="text" name="gender" class="form-control" value="{{$model->gender}}" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="description">Descripción</label>
                    <input type="text" name="description" class="form-control" value="{{$model->description}}" />
                  </div>
                </div>
                <div class="col-md-6">
                  <br>
                  <div class="input-group">
                    <label for="export">
                      Exportar Datos a Excel
                      <input type="checkbox" name="export" />
                    </label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <button class="btn btn-light btn-block" type="submit">
                    <i class="fa fa-search"></i>&nbsp;Buscar
                  </button>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
        {!! Form::open(['url' => '/bulletins']) !!}
        {{csrf_field()}}
        <table class="table">
          <thead>
            <th class="col-md-2">Fecha</th>
            <th class="col-md-1">Cliente</th>
            <th class="col-md-2">Subtítulo</th>
            <th class="col-md-3">Medio</th>
            <th class="col-md-4">Título</th>
            <th class="col-md-1">Tendencia</th>
            <th class="col-md-1"></th>
            <th class="col-md-1"></th>
            <th class="col-md-1"></th>
            <th class="col-md-1"></th>
            <th></th>
          </thead>
          <tbody>
        @foreach($news->items() as $item)
            <tr>
              <?php
              $date = new DateTime($item->date);
              $date = $date->format('d/m/Y');
              ?>
              <td style="font-size: 13px">{{$date}}</td>
              <td>
              @if($item->news->client)
                {{$item->news->client->name}}
              @else
                Sin Cliente
              @endif
              </td>
              <td>{{$item->subtitle}}</td>
              <td>
              <span style="-ms-word-break: break-all;word-break: break-all;word-break: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;">
              @if($item->media)
                {{$item->media->name}}
              @else
                Sin medio
              @endif
              </span>
              </td>
              <td>{{$item->title}}</td>
              <td>
                {{App\Http\Html::tendency($item->tendency)}}
              </td>
              <td>
                <a href="{{url('dashboard/news/'.$item->news_id.'/view')}}" class="btn btn-success" title="Ver Noticia">
                  <i class="fa fa-eye"></i>
                </a>
              </td>
              <td>
                <a href="{{url('dashboard/news/' . $item->news_id . '/edit')}}" class="btn btn-light" title="Editar Noticia">
                  <i class="fa fa-pencil"></i>
                </a>
              </td>
              <td>
                <a href="javascript:void(0)" class=" btn btn-danger delete" data-id="{{$item->news_id}}" data-detail-id="{{$item->id}}" title="Eliminar Detalle de Noticia">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
              <td>
                <a href="javascript:void(0)" class=" btn btn-info news-copy" data-id="{{$item->news_id}}" data-detail-id="{{$item->id}}" title="Copiar noticia">
                  <i class="fa fa-copy"></i>
                </a>
              </td>
              <td>
                <input type="checkbox" name="news_detail_id_{{$item->id}}" value="{{$item->id}}" />
              </td>
            </tr>
        @endforeach
          </tbody>
        </table>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::select('client_id', $clients, null, [
                'class' => 'form-control'
              ]) !!}
            </div>
          </div>
          <div class="col-md-4">
            <button class="btn btn-success">
              Generar Boletín
            </button>
          </div>
        </div>
        {!! Form::close() !!}
        <center>
          {{-- {!! App\Http\Html::paginator($news, '/dashboard/news') !!} --}}
        </center>
      </div>
    </div>
  </div>
</div>
<div id="clientModal"></div>
@stop

@section('scripts')
<script src="{{asset('assets/vendors/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/vendors/js/bootstrap-datepicker.es.min.js')}}"></script>
<script src="{{asset('assets/vendors/js/react.min.js')}}"></script>
<script src="{{asset('assets/js/build.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
  $http = MyApp.$http;
  $('.datepicker').datepicker({
      format: 'dd/mm/yyyy',
      language: 'es',
      orientation: "top right",
      todayHighlight: true,
      autoclose: true
    });

  var modalOptions = {
    clients: [],
    onNewsCopied: function(res) {
      var messages = document.getElementById('messages');
        messages.innerHTML =  '<div class="alert alert-info alert-dismissable">'+
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'+
        '&times;'+
        '</button>'+
        'Noticia copiada exitosamente.'+
        '</div>';
      setTimeout(function() {
        window.location = '/dashboard/news/' + res.id + '/edit';
      }, 500);
    }
  }

  $http.get('/clients').then(function(res) {
    var el = document.getElementById('clientModal');
    modalOptions.clients = res;
    var copyNewsModal = React.createElement(MyApp.CopyNewsModal, modalOptions);
    var copyNewsModal = React.render(copyNewsModal, el);
    $('.news-copy').on('click', function(e) {
      var newsId = e.currentTarget.dataset.id;
      copyNewsModal.showModal(newsId);
    });
  });

  $('.delete').on('click', function(e) {
   if(!confirm('Está seguro que desea eliminar este detalle de noticia?')) {return;}
    var newsDetailId = e.currentTarget.dataset.detailId;
    var newsId = e.currentTarget.dataset.id;

    $http.remove('/news/' + newsId + '/details/' + newsDetailId).then(function(res) {
      var messages = document.getElementById('messages');
        messages.innerHTML =  '<div class="alert alert-info alert-dismissable">'+
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'+
        '&times;'+
        '</button>'+
        'Detalle de noticia eliminado exitosamente.'+
        '</div>';
        setTimeout(function() {
          window.location = '/dashboard/news/';
        }, 500);
    }, function(err) {})
  });
});
</script>
@stop
@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/bootstrap-datepicker.min.css')}}">
@stop
