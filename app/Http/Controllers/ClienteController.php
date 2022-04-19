<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class ClienteController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Cliente::orderBy('nombre', 'asc');

        $nombre = $request->query('nombre');
        if ($nombre) {
            $query->where('nombre', 'LIKE', '%'.$nombre.'%');
        }

        $telefono = $request->query('telefono');
        if ($telefono) {
            $query->where('telefono', 'LIKE', '%'.$telefono.'%');
        }

        $ciudad = $request->query('ciudad');
        if ($ciudad) {
            $query->where('ciudad', 'LIKE', '%'.$ciudad.'%');
        }

        $cedula = $request->query('cedula');
        if ($cedula) {
            $query->where('cedula', 'LIKE', '%'.$cedula.'%');
        }

        $activo = $request->query('activo');
        if ($activo) {
            $query->where('activo', 'LIKE', '%'.$activo.'%');
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
        $nombre = $request->input("nombre");
        $telefono = $request->input("telefono");
        $ciudad = $request->input("ciudad");
        $cedula = $request->input("cedula");

        $validator = Validator::make($request->all(), [
            'nombre'  => 'required',
            'telefono'  => 'required',
            'ciudad'  => 'required',
            'cedula'  => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $cliente = new Cliente();
        $cliente->nombre = $nombre;
        $cliente->telefono = $telefono;
        $cliente->ciudad = $ciudad;
        $cliente->cedula = $cedula;
        $cliente->activo = 'S';

        if ($cliente->save()) {
            return $this->sendResponse(true, 'Cliente registrado', $cliente, 201);
        }
        
        return $this->sendResponse(false, 'Cliente no registrado', null, 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (is_object($cliente)) {
            return $this->sendResponse(true, 'Se listaron exitosamente los registros', $cliente, 200);
        }

        return $this->sendResponse(false, 'No se encontro el cliente', null);

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
        $nombre = $request->input("nombre");
        $telefono = $request->input("telefono");
        $ciudad = $request->input("ciudad");
        $cedula = $request->input("cedula");

        $validator = Validator::make($request->all(), [
            'nombre'  => 'required',
            'telefono'  => 'required',
            'ciudad'  => 'required',
            'cedula' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $cliente = Cliente::find($id);
        if ($cliente) {
            $cliente->nombre = $nombre;
             $cliente->telefono = $telefono;
             $cliente->ciudad = $ciudad;
             $cliente->cedula = $cedula;
             $cliente->activo = 'S';
            if ($cliente->save()) {
                return $this->sendResponse(true, 'Cliente actualizado', $cliente, 200);
            }
            
            return $this->sendResponse(false, 'Cliente no actualizado', null, 400);
        }
        
        return $this->sendResponse(false, 'No se encontro la Cliente', null, 404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if ($cliente) {
            $cliente->activo = ($cliente->activo == 'S') ? 'N' : 'S';
            
            if ($cliente->update()) {
                return $this->sendResponse(true, 'Cliente eliminado', $cliente, 200);
            }
            
            return $this->sendResponse(false, 'Cliente Eliminado', $cliente, 400);
        }
        return $this->sendResponse(true, 'No se encontro el cliente', $cliente, 404);

    }
}
