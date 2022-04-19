<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Impresion a cobrar</title>
        <link rel="stylesheet" href="C:\Users\baezn\Desktop\Bakend-2\api\resources\css\estilos.css">
    </head>
    <body>
        <h1>Lista de Cobranza</h1> 
       <table border="1" frame="void" style="width: 700px; border-collapse: collapse; ">
       <thead>
        <tr>
          <th>Nombre</th>
          <th>Cantidad Cuota</th>
          <th>Monto</th>
          <th>Mora</th>
          <th>Total</th>
          <th>Accion</th>
        </tr>
    </thead>
          <tbody>
          @foreach($listas as $lista)
          <tr> 
              <td>{{$lista->cliente->nombre}}</td>
              <td>{{$lista->n_cuota}}</td>
              <td>{{number_format(intval($lista->monto))}}</td>
              <td>{{number_format(intval($lista->orden))}}</td>
              <td>{{number_format(intval($lista->monto + $lista->orden))}}</td>
              <td>&nbsp;</td>
          </tr>
              @endforeach
              </tbody>

       </table>
    </body>
</html>