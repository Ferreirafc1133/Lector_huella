
<?php
session_start();
$privilege = $_SESSION['privilege'];

?>

<!doctype html>
<html lang="es">

<head>
    <!-- Metaetiquetas requeridas -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../src/css/bootstrap.css">
    <link rel="stylesheet" href="../../src/css/custom.css">

    <title>Aplicación Web de Huellas Dactilares</title>
</head>

<body>
    <!-- JavaScript opcional -->
    <!-- jQuery primero, luego Popper.js, luego Bootstrap JS -->
    <script src="../../src/js/jquery-3.5.0.min.js"></script>
    <script src="../../src/js/bootstrap.bundle.js"></script>
    <script src="../../src/js/es6-shim.js"></script>
    <script src="../../src/js/websdk.client.bundle.min.js"></script>
    <script src="../../src/js/fingerprint.sdk.min.js"></script>
    <script src="../../src/js/custom.js"></script>

    <div class="row justify-content-end mt-3">
        <div class="col-auto">
            <button id="logoutButton" type="button" class="btn btn-warning" onclick="cerrarSesion()">Cerrar Sesión</button>
        </div>
    </div>
    <div class="container">
        <div id="controls" class="row justify-content-center mx-5 mx-sm-0 mx-lg-5">
            <div class="col-sm mb-2 mr-sm-5">
                <button id="verifyIdentityButton" type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#verifyIdentity" onclick="beginIdentification()">Verificar Identidad</button>
            </div>
            <?php if ($privilege <= 1): ?>
            <div class="col-sm mb-2 ml-sm-5">
                <button id="createEnrollmentButton" type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#createEnrollment" onclick="beginEnrollment()">Crear Inscripción</button>
            </div>
        </div>
        <div class="col-sm mb-2 mr-sm-5">
            <button id="showRegistro" type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#verRegistro" onclick="showIdentification()">Ver Registros</button>
        </div>
        <?php endif; ?>
    </div>
</body>

<section>
    <!-- Sección de Crear Inscripción -->
    <div class="modal fade" id="createEnrollment" data-backdrop="static" tabindex="-1" aria-labelledby="createEnrollmentTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title my-text my-pri-color" id="createEnrollmentTitle">Crear Inscripción</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" onclick="clearCapture()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" onsubmit="return false">
                        <div id="enrollmentStatusField" class="text-center">
                            <!-- El estado de la inscripción se mostrará aquí -->
                        </div>
                        <div class="form-row mt-3">
                            <div class="col mb-3 mb-md-0 text-center">
                            <label for="enrollReaderSelect" class="my-text7 my-pri-color">Elegir Lector de Huellas Dactilares</label>
                            <select name="readerSelect" id="enrollReaderSelect" class="form-control" disabled onclick="beginEnrollment()">
                                <option selected>Seleccionar Lector de Huellas Dactilares</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-row mt-2">
                            <div class="col mb-3 mb-md-0 text-center">
                                <label for="userID" class="my-text7 my-pri-color">Especificar ID de Usuario</label>
                                <select id="userID" class="form-control">
                                    <option value="">USUARIOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mt-1">
                            <div class="col text-center">
                                <p class="my-text7 my-pri-color mt-3">Capturar Dedo Índice</p>
                            </div>
                        </div>
                        <div id="indexFingers" class="form-row justify-content-center">
                            <div id="indexfinger1" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="indexfinger2" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="indexfinger3" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="indexfinger4" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                        </div>
                        <div class="form-row mt-1">
                            <div class="col text-center">
                                <p class="my-text7 my-pri-color mt-5">Capturar Dedo Medio</p>
                            </div>
                        </div>
                        <div id="middleFingers" class="form-row justify-content-center">
                            <div id="middleFinger1" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="middleFinger2" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="middleFinger3" class="col mb-3 mb-md-0 text-center">
                                <span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                            <div id="middleFinger4" class="col mb-3 mb-md-0 text-center" value="true">
                                <span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                        </div>
                        <div class="form-row m-3 mt-md-5 justify-content-center">
                            <div class="col-4">
                                <button class="btn btn-primary btn-block my-sec-bg my-text-button py-1" type="submit" onclick="beginCapture()">Iniciar Captura</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-primary btn-block my-sec-bg my-text-button py-1" type="submit" onclick="serverEnroll()">Inscribir</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary btn-outline-warning btn-block my-text-button py-1 border-0" type="button" onclick="clearCapture()">Limpiar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-row">
                        <div class="col">
                            <button class="btn btn-secondary my-text8 btn-outline-danger border-0" type="button" data-dismiss="modal" onclick="clearCapture()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <!-- Sección de Verificación de Identidad -->
    <div id="verifyIdentity" class="modal fade" data-backdrop="static" tabindex="-1" aria-labelledby="verifyIdentityTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title my-text my-pri-color" id="verifyIdentityTitle">Verificación de Identidad</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" onclick="clearCapture()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" onsubmit="return false">
                        <div id="verifyIdentityStatusField" class="text-center">
                            <!-- El estado de la verificación de identidad se mostrará aquí -->
                        </div>
                        <div class="form-row mt-3">
                            <div class="col mb-3 mb-md-0 text-center">
                            <label for="verifyReaderSelect" class="my-text7 my-pri-color">Elegir Lector de Huellas Dactilares</label>
                            <select name="readerSelect" id="verifyReaderSelect" class="form-control" disabled>
                                <option selected>Seleccionar Lector de Huellas Dactilares</option>
                            </select>
                            </div>
                        </div>
                        
                        <div class="form-row mt-4">
                            <div class="col mb-md-0 text-center">
                                <select id="userIDVerify" class="form-control mt-1" style="display: none;">
                                    <option value="">USUARIOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col text-center">
                                <p class="my-text7 my-pri-color mt-1">Capturar Dedo para Verificación</p>
                            </div>
                        </div>
                        <div id="verificationFingers" class="form-row justify-content-center">
                            <div id="verificationFinger" class="col mb-md-0 text-center">
                                <span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>
                            </div>
                        </div>
                        <div class="form-row mt-3" id="userDetails">
                            <!-- Aquí se mostrarán los detalles del usuario -->
                        </div>
                        <div class="form-row m-3 mt-md-5 justify-content-center">
                            <div class="col-4">
                                <button class="btn btn-primary btn-block my-sec-bg my-text-button py-1" type="submit" onclick="captureForIdentify()">Iniciar Captura</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-primary btn-block my-sec-bg my-text-button py-1" type="submit" onclick="serverIdentify()">Identificar</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary btn-outline-warning btn-block my-text-button py-1 border-0" type="button" onclick="clearCapture()">Limpiar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="form-row">
                        <div class="col">
                            <button class="btn btn-secondary my-text8 btn-outline-danger border-0" type="button" data-dismiss="modal" onclick="clearCapture()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Ver registros -->
<section>
    <div class="modal fade" id="verRegistro" data-backdrop="static" tabindex="-1" aria-labelledby="verRegistroTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verRegistroTitle">Registros</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Selector de rango de fechas -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fechaInicio">Fecha de inicio:</label>
                            <input type="date" class="form-control" id="fechaInicio">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fechaFin">Fecha de fin:</label>
                            <input type="date" class="form-control" id="fechaFin">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="opcionSelector">Seleccionar Empleado:</label>
                        <select class="form-control" id="opcionSelector">
                            <option value="todas">Todas</option>
                        </select>
                    </div>
                    <!-- Botón para traer registros -->
                    <button type="button" class="btn btn-primary" onclick="traerRegistros()">Traer Registros</button>
                </div>
                <div class="modal-body" id="tablaRegistros">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</section>


</html>
<style>
html, body {
    height: 100%; /* Esto asegura que el body tome todo el alto del viewport */
    margin: 0; /* Elimina el margen predeterminado */
    padding: 0; /* Elimina el padding predeterminado */
}

body {
    background-image: url('https://cdn5.f-cdn.com/contestentries/1108779/15284413/5994ef1193f43_thumb900.jpg');
    background-size: cover; /* Cubre el tamaño completo del contenedor */
    background-repeat: no-repeat; /* No repetir la imagen */
    background-position: center center; /* Centrar la imagen */
    background-attachment: fixed; /* Fija el fondo para que no se mueva con el scroll */
}


</style>