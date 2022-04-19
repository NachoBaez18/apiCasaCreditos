<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetallePedido;
use App\Models\Arqueo;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class DetallePedidoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DetallePedido::with(['pedido' => function ($querys){
            $querys->orderBy('fecha_entrega','desc');
        }]);

        $idPedido = $request->query('id_pedido');
        if ($idPedido) {
            $query->where('id_pedido', '=', $idPedido);
        }

        $fecha_vencimiento = $request->query('fecha_vencimiento');
        if ($fecha_vencimiento) {
            $query->where('fecha_vencimiento', 'LIKE', '%'.$fecha_vencimiento.'%');
        }

        $cuota_numero = $request->query('cuota_numero');
        if ($cuota_numero) {
            $query->where('cuota_numero', '=', $cuota_numero);
        }
  
        $monto = $request->query('monto');
        if ($monto) {
            $query->where('monto', '=', $monto);
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
        $id_pedido = $request->input("id_pedido");
        $fecha_vencimiento = $request->input("fecha_vencimiento");
        $monto = $request->input("monto");
        $cuota_numero = $request->input("cuota_numero");

        $validator = Validator::make($request->all(), [
            'id_pedido'  => 'required',
            'fecha_vencimiento'  => 'required',
            'monto'  => 'required',
            'cuota_numero' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $detallepedido = new DetallePedido();
        $detallepedido->id_pedido = $id_pedido;
        $detallepedido->fecha_vencimiento = $fecha_vencimiento;
        $detallepedido->monto = $monto;
        $detallepedido->cuota_numero = $cuota_numero;
        $detallepedido->cancelado ='N';

        if ($detallepedido->save()) {
            return $this->sendResponse(true, 'Pedido detalle registrado', $detallepedido, 201);
        }
        
        return $this->sendResponse(false, 'Pedido detalle no registrado', null, 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detallepedido = DetallePedido::find($id);

        if (is_object($detallepedido)) {
            return $this->sendResponse(true, 'Se listaron exitosamente los registros', $detallepedido, 200);
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
        $id_pedido = $request->input("id_pedido");
        $fecha_vencimiento = $request->input("fecha_vencimiento");
        $monto = $request->input("monto");
        $cuota_numero = $request->input("cuota_numero");
        $cancelado = $request->input("cancelado");
        $mora = $request->input("mora");
        $moraDias = $request->input("moraDias");
        $moraCancelado = $request->input("moraCancelado");

        $validator = Validator::make($request->all(), [
            'id_pedido'  => 'required',
            'fecha_vencimiento'  => 'required',
            'monto'  => 'required',
            'cuota_numero' => 'required',
            'mora'  => 'required',
            'moraDias' => 'required',
            'moraCancelado' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $arqueo = Arqueo::orderBy('created_at', 'desc')->take(1)->first();
        if($arqueo->cerrado === 'N'){
      


        $detallepedido = DetallePedido::find($id);
        if ($detallepedido) {
            $detallepedido->id_pedido = $id_pedido;
            $detallepedido->fecha_vencimiento = $fecha_vencimiento;
            $detallepedido->monto = $monto;
            $detallepedido->cuota_numero = $cuota_numero;
            $detallepedido->cancelado =$cancelado;
            $detallepedido->mora =$mora;
            $detallepedido->moraDias =$moraDias;
            $detallepedido->moraCancelado =$moraCancelado;
            if ($detallepedido->save()) {
                return $this->sendResponse(true, 'Detalle actualizado', $detallepedido, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no actualizado', null, 400);
        }
        
        return $this->sendResponse(false, 'No se encontro el detalle', null, 404);
    }
    return $this->sendResponse(false, 'Favor cargar la caja', $arqueo, 404);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detallepedido = DetallePedido::find($id);

        if ($detallepedido) {
            $detallepedido->cancelado = ($detallepedido->cancelado == 'N') ? 'S' : 'S';
            
            if ($detallepedido->update()) {
                return $this->sendResponse(true, 'Detalle eliminado', $detallepedido, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no eliminado', $detallepedido, 400);
        }
        
        return $this->sendResponse(true, 'No se encontro pedido', $detallepedido, 404);
    }

    public function pagadoParcial(Request $request, $id){

        $id_pedido = $request->input("id_pedido");
        $fecha_vencimiento = $request->input("fecha_vencimiento");
        $monto = $request->input("monto");
        $cuota_numero = $request->input("cuota_numero");
        $cancelado = $request->input("cancelado");
        $mora = $request->input("mora");
        $moraDias = $request->input("moraDias");
        $moraCancelado = $request->input("moraCancelado");

        $validator = Validator::make($request->all(), [
            'id_pedido'  => 'required',
            'fecha_vencimiento'  => 'required',
            'monto'  => 'required',
            'cuota_numero' => 'required',
            'mora'  => 'required',
            'moraDias' => 'required',
            'moraCancelado' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }
        $arqueo = Arqueo::orderBy('created_at', 'desc')->take(1)->first();
        if($arqueo->cerrado === 'N'){
        $arqueo->cobrado = $monto;
        $arqueo->update();


        $detallepedido = DetallePedido::find($id);
        if ($detallepedido) {
            $detallepedido->id_pedido = $id_pedido;
            $detallepedido->fecha_vencimiento = $fecha_vencimiento;
            $detallepedido->monto = $monto;
            $detallepedido->cuota_numero = $cuota_numero;
            $detallepedido->cancelado =$cancelado;
            $detallepedido->mora =$mora;
            $detallepedido->moraDias =$moraDias;
            $detallepedido->moraCancelado =$moraCancelado;
            if ($detallepedido->save()) {
                return $this->sendResponse(true, 'Detalle actualizado', $detallepedido, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no actualizado', null, 400);
         }
        
        return $this->sendResponse(false, 'No se encontro el detalle', null, 404);
    }
     return $this->sendResponse(false, 'Favor cargar la caja', $arqueo, 404);
 }


    public function pagadoTotal(Request $request, $id){

        $id_pedido = $request->input("id_pedido");
        $fecha_vencimiento = $request->input("fecha_vencimiento");
        $monto = $request->input("monto");
        $cuota_numero = $request->input("cuota_numero");
        $cancelado = $request->input("cancelado");
        $mora = $request->input("mora");
        $moraDias = $request->input("moraDias");
        $moraCancelado = $request->input("moraCancelado");

        $validator = Validator::make($request->all(), [
            'id_pedido'  => 'required',
            'fecha_vencimiento'  => 'required',
            'monto'  => 'required',
            'cuota_numero' => 'required',
            'mora'  => 'required',
            'moraDias' => 'required',
            'moraCancelado' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $detallepedido = DetallePedido::find($id);
        $arqueo = Arqueo::orderBy('created_at', 'desc')->take(1)->first();
        if($arqueo->cerrado === 'N'){
        $arqueo->cobrado = $monto + $mora;
        $arqueo->update();

        if ($detallepedido) {
            $detallepedido->id_pedido = $id_pedido;
            $detallepedido->fecha_vencimiento = $fecha_vencimiento;
            $detallepedido->monto = $monto;
            $detallepedido->cuota_numero = $cuota_numero;
            $detallepedido->cancelado =$cancelado;
            $detallepedido->mora =$mora;
            $detallepedido->moraDias =$moraDias;
            $detallepedido->moraCancelado =$moraCancelado;
            if ($detallepedido->save()) {
                return $this->sendResponse(true, 'Detalle actualizado', $detallepedido, 200);
            }
            
            return $this->sendResponse(false, 'Detalle no actualizado', null, 400);
         }
        
        return $this->sendResponse(false, 'No se encontro el detalle', null, 404);
     }

        return $this->sendResponse(false, 'Favor cargar la caja',null, 404);
  }

}
