<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Impresion a entregar</title>
        <link rel="stylesheet" href="C:\Users\baezn\Desktop\Bakend-2\api\resources\css\estilos.css">
    </head>
    <body>
        <h1>Lista de Entrega</h1> 
       <table border="1" frame="void" style="width: 700px; border-collapse: collapse; ">
       <thead>
        <tr>
          <th>Nombre</th>
          <th>N° Cuotas</th>
          <th>Monto</th>
          <th>Accion</th>
        </tr>
    </thead>
          <tbody>
          @foreach($listas as $lista)
          <tr> 
              <td>{{$lista->cliente->nombre}}</td>
              <td>{{$lista->n_cuota}}</td>
              <td>{{number_format(intval($lista->monto))}}</td>
              <td>&nbsp;</td>
          </tr>
              @endforeach
              </tbody>

       </table>
    </body>
</html>