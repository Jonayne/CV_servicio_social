@csrf

<div class="container bg-primary text-black py-2">
    <div class="form-group">
        <label for="nombre">Nombre del curso</label>
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $course->nombre)}}">
        <br>
        <label for="anio">Año del curso</label>
        <input type="text" name="anio" id="anio"  class="form-control" value="{{ old('anio', $course->anio)}}">
        <br>
        <label for="documento_obtenido">Documento obtenido</label>
        <input type="text" name="documento_obtenido" id="documento_obtenido" class="form-control" value="{{old('documento_obtenido', $course->documento_obtenido)}}">
        <br>
        <label for="es_curso_tecnico">Tipo de curso</label>
        <select name="es_curso_tecnico" id="es_curso_tecnico" class="form-control">
            @if(!isset($course->es_curso_tecnico))
                <option value="" selected>Escoger...</option>
                <option value="true">Curso técnico</option>
                <option value="false">Curso de formación docente</option>
            @elseif ($course->es_curso_tecnico)
                <option value="true" selected>Curso técnico</option>
                <option value="false">Curso de formación docente</option>
            @else
                <option value="true">Curso técnico</option>
                <option value="false" selected>Curso de formación docente</option>
            @endif

        </select><br><br>
        <div class="text-center">
            <a class="btn btn-dark btn-lg" href="{{route(session()->get('previous_url') ?? 'home')}}"> Cancelar </a>
            &nbsp;
            <button class="btn btn-success btn-lg" name="formNum" value="3" type="submit"> {{ $btnTxt }} </button>
        </div>
    </div>
</div>
