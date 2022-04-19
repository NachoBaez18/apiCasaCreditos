<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arqueo;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class ArqueoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Arqueo::orderBy('created_at', 'desc');

        $fecha = $request->query('fecha');
        if ($fecha) {
            $query->where('created_at', '=', $fecha);
        }


        $paginar = $request->query('paginar');
        $listar = (boolval($paginar)) ? 'paginate' : 'get';

        $data = $query->$listar();
        
        return $this->sendResponse(true, 'Listado obtenido exitosamente', $data, 200);
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
    public function store(Request $request)
    {
        $caja = $request->input("caja");


        $validator = Validator::make($request->all(), [
            'caja'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $arqueo = new Arqueo();
        $arqueo->caja = $caja;
        $arqueo->cobrado =0;
        $arqueo->entregado =0;
        $arqueo->arqueoDia = 0;
        $arqueo->cerrado = 'N';

        if ($arqueo->save()) {
            return $this->sendResponse(true, 'Arqueo registrado', $arqueo, 201);
        }
        
        return $this->sendResponse(false, 'Arqueo no registrado', null, 400);
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

        $caja = $request->input("caja");

        $validator = Validator::make($request->all(), [
            'caja'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }
        $arqueo = Arqueo::find($id);

        if ($arqueo) {
            $arqueo->caja = $caja;
            
            if ($arqueo->save()) {
                return $this->sendResponse(true, 'Arqueo actualizado', $arqueo, 200);
            }
            
            return $this->sendResponse(false, 'Arqueo no actualizado', $arqueo, 400);
        }
        return $this->sendResponse(true, 'No se encontro el arqueo', $arqueo, 404);

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

    public function ultimoArqueo(Request $request){
        $query = Arqueo::orderBy('created_at', 'desc')->take(1)->first();

       

        $data = $query;
        
        return $this->sendResponse(true, 'Listado obtenido exitosamente', $data, 200);
    }

    public function cerrarCaja(Request $request ,$id){
        $caja = $request->input("caja");
        $cobrado = $request->input("cobrado");
        $entregado = $request->input("entregado");
        $arqueoDia = $request->input("arqueoDia");
        $cerrado = $request->input("cerrado");


        $validator = Validator::make($request->all(), [
            'caja'  => 'required',
            'cobrado' => 'required',
            'entregado' => 'required',
            'arqueoDia' => 'required',
            'cerrado' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }
        $arqueo = Arqueo::find($id);

        if ($arqueo) {
            $arqueo->caja = $caja;
            $arqueo->entregado = $entregado;
            $arqueo->cobrado = $cobrado;
            $arqueo->arqueoDia = $arqueoDia;
            $arqueo->cerrado = $cerrado;
            
            if ($arqueo->save()) {
                return $this->sendResponse(true, 'Arqueo actualizado', $arqueo, 200);
            }
            
            return $this->sendResponse(false, 'Arqueo no actualizado', $arqueo, 400);
        }
        return $this->sendResponse(true, 'No se encontro el arqueo', $arqueo, 404);
    }
}
