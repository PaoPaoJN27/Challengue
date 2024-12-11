$(document).ready(function(){


    $('#btnRegistro').on('click', function(){

       let datos = $('#usuarioFormulario').serialize();

       $.ajax({

            type: 'POST',
            url: '../../backend/ConexionUsuarios/insertarUsuario.php' ,
            data: datos,
            success: function(){
                alert("Se registro correctamente");
            }

       })


    })



})