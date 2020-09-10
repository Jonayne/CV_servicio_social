<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProfessorRequest;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller {

    protected $cat_pago_list = ['Académico de otra dependencia de la UNAM que expide recibo de honorarios (Tiempo completo o asignatura) (Ei)',
                        'Interno Auxiliar que expide recibo de honorarios (IAX)',
                        'Interno Asociado que expide recibo de honorarios (IAS)',
                        'Interno Titular que expide recibo de honorarios (ITT)',
                        'Interno Investigador que expide recibo de honorarios (IIN)',
                        'Interno Investigador que emite factura (IIF)',
                        'No tiene relación con la UNAM y SÍ emite recibo de honorarios (Evi)',
                        'No tiene relación con la UNAM Emite factura (Ev)',
                        'Es becario de la DGTIC que expide recibo de honorarios (Eiii)',
                        'Es personal de honorarios de la DGTIC que expide recibo de honorarios (Eii)',
                        'Es Académico de la UNAM que emite factura (Eviii)',
                        'Caso especial (Z)'];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {   
        if( Gate::denies('buscar-profesor')) {
                  return redirect()->route('home')->with('status', 'No tiene permisos para realizar esta acción.')
                                                ->with('status_color', 'danger');
        }

        $cat_pago_list = $this->cat_pago_list;

        return view('search/search', compact('cat_pago_list'));
    }

    public function searchOnDB(SearchProfessorRequest $request) {   
        // Validamos los datos.
        $request->validated();

        $nombre = $request->name;
        $correo = $request->email;
        $rfc = $request->rfc;
        $curp = $request->curp;
        $categoria_de_pago = $request->categoria_de_pago;

        // Al ser los profesores los únicos con un curriculum, son los 
        // únicos que entrarán en el filtro.
        $users = DB::table('curricula')->
                    leftJoin('users', 'curricula.user_id', '=', 'users.id')
                    ->select('curricula.id', 'curricula.nombre', 'curricula.apellido_paterno', 'curricula.apellido_materno', 
                        'users.nombre', 'users.apellido_paterno', 'users.apellido_materno', 'curp',
                        'rfc', 'users.email', 'curricula.email_personal', 'status', 'categoria_de_pago');
    
        //$tabla = $users->get()->all();

        if($nombre) {
            // ILIKE sólo funciona en Postgresql, busca sin diferenciar entre mayúsculas y minúsculas. Otra 
            // solución es cambiar la codificaciones de la tabla a ci_spanish_algo...

            // Aquí el closure nos ayuda a parentizar la consulta
            // (pues el OR y AND tienen la misma precedencia y pueden generar ambigüedad sin paréntesis)
            $users->where(function($query) use ($nombre) {
                $query->orWhere('curricula.nombre', 'ILIKE', '%'.$nombre.'%')->
                        orWhere('curricula.apellido_paterno', 'ILIKE', '%'.$nombre.'%')->
                        orWhere('curricula.apellido_materno', 'ILIKE', '%'.$nombre.'%')->
                        orWhere('users.nombre', 'ILIKE', '%'.$nombre.'%')->
                        orWhere('users.apellido_paterno', 'ILIKE', '%'.$nombre.'%')->
                        orWhere('users.apellido_materno', 'ILIKE', '%'.$nombre.'%');
            });
                
        }

        if($correo) {
            $users->where(function($query) use ($correo) {
                $query->orWhere('users.email', 'ILIKE', '%'.$correo.'%')->
                        orWhere('curricula.email_personal', 'ILIKE', '%'.$correo.'%');
            });
        }

        if($rfc) {
            $users->where('rfc', 'ILIKE', '%'.$rfc.'%');
        }

        if($curp) {
            $users->where('curp', 'ILIKE', '%'.$curp.'%');
        }

        if($categoria_de_pago) {
            $users->where('categoria_de_pago', ''.$categoria_de_pago.'');
        }

        $users->orderBy('curricula.id')->orderByDesc('status');

        return view('search/result')->
                with('result', $users->get())->
                with('nombre', $nombre)->
                with('rfc', $rfc)->
                with('curp', $curp)->
                with('correo', $correo)->
                with('categoria_de_pago', $categoria_de_pago);
    }
}
