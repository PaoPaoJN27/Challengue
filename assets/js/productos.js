$(document).ready(function(){


    $('#btnProducto').on('click', function(){

       let datos = $('#productForm').serialize();

       $.ajax({

            type: 'POST',
            url: '../../backend/ConexionProductos/insertarProductos.php' ,
            data: datos,
            success: function(){
                alert("Producto Agregado");
            }

       })


    })



})