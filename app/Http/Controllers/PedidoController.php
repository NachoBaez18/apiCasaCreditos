<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Arqueo;
use App\Models\DetallePedido;
use Validator;
use App\Http\Controllers\BaseController as BaseController;
use Carbon\Carbon;
use PHPJasper\PHPJasper; 
use Luecano\NumeroALetras\NumeroALetras;
use PDF;


class PedidoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente'])->orderBy('orden', 'asc');
            
        $idCliente = $request->query('id_cliente');
        if ($idCliente) {
            $query->where('id_cliente', '=', $idCliente);
        }
     
        $fecha_entrega = $request->query('fecha_entrega');
        if ($fecha_entrega) {
            $query->where('fecha_entrega', '=', $fecha_entrega);
        }

        $entregado = $request->query('entregado');
        if ($entregado) {
            $query->where('entregado', 'LIKE', '%'.$entregado.'%');
        }

        $n_cuota = $request->query('n_cuota');
        if ($n_cuota) {
            $query->where('n_cuota', 'LIKE', '%'.$n_cuota.'%');
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
        $orden = Pedido::orderBy('created_at', 'desc')->take(1)->first();
        $id_cliente = $request->input("id_cliente");
        $fecha_entrega = $request->input("fecha_entrega");
        $monto = $request->input("monto");
        $n_cuota = $request->input("n_cuota");

        $validator = Validator::make($request->all(), [
            'id_cliente'  => 'required',
            'fecha_entrega'  => 'required',
            'monto'  => 'required',
            'n_cuota' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $pedido = new Pedido();
        $pedido->id_cliente = $id_cliente;
        $pedido->fecha_entrega = $fecha_entrega;
        $pedido->monto = $monto;
        $pedido->n_cuota = $n_cuota;
        $pedido->orden = $orden->orden + 1;
        $pedido->entregado = 'N';
        $pedido->cancelado ='N';

        if ($pedido->save()) {
            return $this->sendResponse(true, 'Pedido registrado', $pedido, 201);
        }
        
        return $this->sendResponse(false, 'Pedido no registrado', null, 400);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pedido = Pedido::find($id);

        if (is_object($pedido)) {
            return $this->sendResponse(true, 'Se listaron exitosamente los registros', $pedido, 200);
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
        
        $id_cliente = $request->input("id_cliente");
        $fecha_entrega = $request->input("fecha_entrega");
        $monto = $request->input("monto");
        $n_cuota = $request->input("n_cuota");

        $validator = Validator::make($request->all(), [
            'id_cliente'  => 'required',
            'fecha_entrega'  => 'required',
            'monto'  => 'required',
            'n_cuota' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Error de validacion', $validator->errors(), 400);
        }

        $pedido = Pedido::find($id);
        if ($pedido) {
            $pedido->id_cliente = $id_cliente;
            $pedido->fecha_entrega = $fecha_entrega;
            $pedido->monto = $monto;
            $pedido->n_cuota = $n_cuota;
            $pedido->entregado = 'N';
            $pedido->cancelado ='N';
            if ($pedido->save()) {
                return $this->sendResponse(true, 'Pedido actualizado', $pedido, 200);
            }
            
            return $this->sendResponse(false, 'Pedido no actualizado', null, 400);
        }
        
        return $this->sendResponse(false, 'No se encontro el pedido', null, 404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pedido = Pedido::find($id);

        if ($pedido) {
            $pedido->cancelado = ($pedido->cancelado == 'N') ? 'S' : 'S';
            
            if ($pedido->update()) {
                return $this->sendResponse(true, 'Pedido eliminado', $pedido, 200);
            }
            
            return $this->sendResponse(false, 'Pedido no eliminado', $pedido, 400);
        }
        
        return $this->sendResponse(true, 'No se encontro pedido', $pedido, 404);

    }
    public function entregado(Request $request, $id)
    { 
        $pedido = Pedido::find($id);
        $arqueo = Arqueo::orderBy('created_at', 'desc')->take(1)->first();
        if($arqueo->cerrado === 'N'){

        $arqueo->entregado = $arqueo->entregado + $pedido->monto;
        $arqueo->update();

        if ($pedido) {
            $pedido->entregado = ($pedido->entregado == 'N') ? 'S' : 'S';
           
            if ($pedido->update()) {
                    for($i=0,$dias=7 ; $i < $pedido->n_cuota; $i++,$dias= $dias + 7){
                            $detalle = new DetallePedido();
                            $date = $pedido->fecha_entrega;
                            $detalle->id_pedido = $pedido->id;
                            $fechaNueva = strtotime ( '+'.$dias.' day' , strtotime($date));
                            $fechaNueva = date ( 'Y-m-j' , $fechaNueva );
                            $detalle->fecha_vencimiento = $fechaNueva;
                            $detalle->monto = $pedido->monto / $pedido->n_cuota;
                            $detalle->cuota_numero = $i + 1;
                            $detalle->cancelado = 'N';
                            $detalle->mora = 0;
                            $detalle->moraDias = 0;
                            $detalle->moraCancelado = 'N';
                            $detalle->save();
                    }
                return $this->sendResponse(true, 'Pedido entregado', $arqueo, 200);
            }
            
            return $this->sendResponse(false, 'Pedido no entregado', $pedido, 400);
            }
        
             return $this->sendResponse(false, 'No se encontro pedido', $pedido, 404);
        }
        return $this->sendResponse(false, 'Favor cargar la caja', $arqueo, 404);
    }


    public function getCobrarhoy(Request $request){
   
        $query = Pedido::whereHas('detalles', function ($q) {
                $fechaVencimiento =Carbon::now();
                $q->where('fecha_vencimiento', '=',$fechaVencimiento->format('Y-m-d'))
                ->where('cancelado', '=', 'N');
            })->with(['cliente','detalles'  => function ($query) {
            $fechaVencimiento =Carbon::now();
            $query->where('fecha_vencimiento', '=', $fechaVencimiento->format('Y-m-d'));
            
    }])->orderBy('orden', 'asc');

        $paginar = $request->query('paginar');
        $listar = (boolval($paginar)) ? 'paginate' : 'get';

        $data = $query->$listar();
        
        return $this->sendResponse(true, 'Listado obtenido exitosamente', $data, 200);
    }

    public function getCobrar(Request $request){
   
        $query = Pedido::whereHas('detalles')
        ->with(['cliente','detalles'  => function ($query) {
            $fechaVencimiento =Carbon::now();
            $query->where('cancelado', '=', 'N');
    }]);

        $paginar = $request->query('paginar');
        $listar = (boolval($paginar)) ? 'paginate' : 'get';

        $data = $query->$listar();
        
        return $this->sendResponse(true, 'Listado obtenido exitosamente', $data, 200);
    

    }    
    public function ultimoArqueo(Request $request){
        $query = Arqueo::orderBy('created_at', 'desc')->take(1);

        $paginar = $request->query('paginar');
        $listar = (boolval($paginar)) ? 'paginate' : 'get';

        $data = $query->$listar();
        
        return $this->sendResponse(true, 'Listado obtenido exitosamente', $data->id, 200);
    }

    public function orden(Request $request){

        $pedidos =  $request->pedidos;
        $contador = 1;
        
        foreach($pedidos as $p){
            $id = $p["id"];

            $pedidoEdit = Pedido::find($id);

            if ($pedidoEdit) {
                $pedidoEdit->orden = $contador;
                $pedidoEdit->save();
                $contador = $contador + 1;
        } 
        
    }
    return $this->sendResponse(true, 'Nuevo orden',null, 200);

}





public function reporte($id)
{

    $pedido = Pedido::with(['cliente'])->find($id);

    if($pedido){

        $fechaEntrega =$pedido->fecha_entrega;
        $monto = $pedido->monto;
        $nombreCliente = $pedido->cliente->nombre;
        $cedula = $pedido->cliente->cedula;
        $direccion = $pedido->cliente->ciudad;

        $separa = explode('-',$fechaEntrega);
        $mes = $separa[1];
        $dia = $separa[2];
        $ano = $separa[0];
    

    $input = base_path().'\app\Reportes\pagare1.jrxml';
    $output = base_path().'\app\Reportes';   

    $formatter = new NumeroALetras();
    $letras = $formatter->toString($monto);
    $meses = array("ENERO","FEBREO","MARZO","ABRIL","MAYO","JUNIO",
                    "JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $mesLetra = $meses[$mes - 1];

    $options = [
        'format' => ['pdf'],
        'params' => [
            'numeroPagare' =>$id,
            'monto' =>$monto,
            'fechaDia' => $dia,
            'fechaMes' => $mesLetra,
            'fechaYear' => $ano,
            'NombreCliente' => $nombreCliente,
            'cedula' => $cedula ,
            'direccion' => $direccion,
           'montoLetras' => $letras,
        ]
    ];
        
    $jasper = new PHPJasper;
    $jasper->process(
        $input,
        $output,
        $options 
    )->execute();
   
    return response()->file($output.'\pagare1.pdf');

    }
    return $this->sendResponse(true, 'No se encontro pedido',null, 200);

}
public function aCobrarPdf(Request $request) {

    $listas = Pedido::whereHas('detalles', function ($q) {
        $fechaVencimiento =Carbon::now();
        $q->where('fecha_vencimiento', '=',$fechaVencimiento->format('Y-m-d'))
        ->where('cancelado', '=', 'N');
    })->with(['cliente','detalles'  => function ($query) {
    $fechaVencimiento =Carbon::now();
    $query->where('fecha_vencimiento', '=', $fechaVencimiento->format('Y-m-d'));
    
}])->get();

$montoTotal = 0;
$contador = 0;
$moraTotal = 0;
foreach($listas as $lista)  {
    foreach($lista->detalles as $detalle){
            $montoTotal =$montoTotal + $detalle->monto;
            $moraTotal = $moraTotal + $detalle->mora;
            $contador = $contador + 1;
    }
        $lista->monto = $montoTotal;
        $lista->n_cuota =$contador;
        $lista->orden = $moraTotal;

        $montoTotal = 0;
        $contador = 0;
        $moraTotal = 0;
}

if ($listas) {
    $pdf = PDF::loadView('pdf', compact('listas'));  
    return $pdf->stream('acobrar.pdf');
     
        }

}

public function aEntregarPdf(Request $request) {
    $fechaVencimiento =Carbon::now();
    $listas = Pedido::with(['cliente'])->orderBy('orden', 'asc')
    ->where('entregado', '=', 'N')
    ->where('fecha_entrega', '=',$fechaVencimiento->format('Y-m-d'))
    ->get();

if ($listas) {
    $pdf = PDF::loadView('entragar', compact('listas'));  
    return $pdf->stream('aentregar.pdf');
     
        }

}


}
