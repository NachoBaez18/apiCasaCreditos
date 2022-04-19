<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mora;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class MoraController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $query = Mora::with(['detallePedido' => function ($querys){
            $querys->orderBy('fecha_vencimiento','desc');
        }]);

        $idPedido = $request->query('id_detallePedido');
        if ($idPedido) {
            $query->where('id_pedido', '=', $idPedido);
        }
  
        $monto = $request->query('monto');
        if ($monto) {
            $query->where('monto', '=', $monto);
        }

        $dias = $request->query('dias');
        if ($dias) {
            $query->where('dias', '=', $dias);
        }


        $cancelado = $request->query('cancelado');
        if ($cancelado) {
            $query->where('cancelado', 'LIKE', '%'.$cancelado.'%');
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
        $id_detallePedido = $request->input("id_detallePedido");
        $monto = $request->input("monto");
        $dias = $request->input("dias");

        $validator = Validator::make($request->all(), [
            'id_detallePedido'  => 'required',
            'monto'  => 'required',
            'dias' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $mora = new Mora();
        $mora->id_detallePedido = $id_detallePedido;
        $mora->monto = $monto;
        $mora->dias = $dias;
        $mora->cancelado ='N';

        if ($mora->save()) {
            return $this->sendResponse(true, 'Mora registrado', $mora, 201);
        }
        
        return $this->sendResponse(false, 'Mora no registrado', null, 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mora = Mora::find($id);

        if (is_object($mora)) {
            return $this->sendResponse(true, 'Se listaron exitosamente los registros', $mora, 200);
        }
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
        $id_detallePedido = $request->input("id_detallePedido");
        $monto = $request->input("monto");
        $dias = $request->input("dias");

        $validator = Validator::make($request->all(), [
            'id_detallePedido'  => 'required',
            'monto'  => 'required',
            'dias' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $mora = Mora::find($id);
        if ($mora) {
            $mora->id_detallePedido = $id_detallePedido;
            $mora->monto = $monto;
            $mora->dias = $dias;
            $mora->cancelado ='N';
            if ($mora->save()) {
                return $this->sendResponse(true, 'Detalle actualizado', $mora, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no actualizado', null, 400);
        }
        
        return $this->sendResponse(false, 'No se encontro el detalle', null, 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mora = Mora::find($id);

        if ($mora) {
            $mora->cancelado = ($mora->cancelado == 'N') ? 'S' : 'S';
            
            if ($mora->update()) {
                return $this->sendResponse(true, 'Detalle eliminado', $mora, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no eliminado', $mora, 400);
        }
        
        return $this->sendResponse(true, 'No se encontro pedido', $mora, 404);
    }
}
