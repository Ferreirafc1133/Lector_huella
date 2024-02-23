/**
 * Custom implementation for the FingerPrint
 * Reader and other JS functions
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-14 17:06:41
 * @version 1.0.0
 */


let currentFormat = Fingerprint.SampleFormat.Intermediate;

let FingerprintSdkTest = (function () {
    function FingerprintSdkTest() {
        let _instance = this;
        this.operationToRestart = null;
        this.acquisitionStarted = false;
        // instantiating the fingerprint sdk here
        this.sdk = new Fingerprint.WebApi;
        this.sdk.onDeviceConnected = function (e) {
            // Detects if the device is connected for which acquisition started
            showMessage("Scan Appropriate Finger on the Reader", "success");
        };
        this.sdk.onDeviceDisconnected = function (e) {
            // Detects if device gets disconnected - provides deviceUid of disconnected device
            showMessage("Device is Disconnected. Please Connect Back");
        };
        this.sdk.onCommunicationFailed = function (e) {
            // Detects if there is a failure in communicating with U.R.U web SDK
            showMessage("Communication Failed. Please Reconnect Device")
        };
        this.sdk.onSamplesAcquired = function (s) {
            // Sample acquired event triggers this function
            storeSample(s);
        };
        this.sdk.onQualityReported = function (e) {
            // Quality of sample acquired - Function triggered on every sample acquired
            //document.getElementById("qualityInputBox").value = Fingerprint.QualityCode[(e.quality)];
        }
    }

    // this is were finger print capture takes place
    FingerprintSdkTest.prototype.startCapture = function () {
        if (this.acquisitionStarted) // Monitoring if already started capturing
            return;
        let _instance = this;
        showMessage("");
        this.operationToRestart = this.startCapture;
        this.sdk.startAcquisition(currentFormat, "").then(function () {
            _instance.acquisitionStarted = true;

            //Disabling start once started
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };
    
    FingerprintSdkTest.prototype.stopCapture = function () {
        if (!this.acquisitionStarted) //Monitor if already stopped capturing
            return;
        let _instance = this;
        showMessage("");
        this.sdk.stopAcquisition().then(function () {
            _instance.acquisitionStarted = false;

            //Disabling stop once stopped
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };

    FingerprintSdkTest.prototype.getInfo = function () {
        let _instance = this;
        return this.sdk.enumerateDevices();
    };

    FingerprintSdkTest.prototype.getDeviceInfoWithID = function (uid) {
        let _instance = this;
        return  this.sdk.getDeviceInfo(uid);
    };
    
    return FingerprintSdkTest;
})();


class Reader{
    constructor(){
        this.reader = new FingerprintSdkTest();
        this.selectFieldID = null;
        this.currentStatusField = null;
        /**
         * @type {Hand}
         */
        this.currentHand = null;
    }

    readerSelectField(selectFieldID){
        this.selectFieldID = selectFieldID;
    }

    setStatusField(statusFieldID){
        this.currentStatusField = statusFieldID;
        
    }

    displayReader(){
        let readers = this.reader.getInfo();
        let id = this.selectFieldID;
        let selectField = document.getElementById(id);
        selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
    
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                
                let expectedReaderId = this.responseText.trim();; 

                readers.then(function(availableReaders){
                    if(availableReaders.length > 0){
                        showMessage("");
                        let found = false;
                        for(let reader of availableReaders){
                            console.log("Lector conectado: ", reader);
                            if(reader === expectedReaderId) {
                                selectField.innerHTML += `<option value="${reader}" selected>${reader}</option>`;
                                found = true;
                            }
                        }
                        if(!found){
                            showMessage("El lector no es el registrado para la plaza.");
                            console.log("Lector esperado: ", expectedReaderId);
                        }
                    }
                    else{
                        showMessage("El lector no se reconoce.");
                    }
                });
            }
        };
    
        xhttp.open("GET", "../../src/core/lectorId.php", true); 
        xhttp.send();
    }
   /*
    displayReader(){
        let readers = this.reader.getInfo();  // grab available readers here
        let id = this.selectFieldID;
        let selectField = document.getElementById(id);
        selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
        readers.then(function(availableReaders){  // when promise is fulfilled
            if(availableReaders.length > 0){
                showMessage("");
                for(let reader of availableReaders){
                    console.log("Lector conectado: ", reader);
                    selectField.innerHTML += `<option value="${reader}" selected>${reader}</option>`;
                }
            }
            else{
                showMessage("Please Connect the Fingerprint Reader");
            }
        })
    }
    */
}

class Hand{
    constructor(){
        this.id = 0;
        this.index_finger = [];
        this.middle_finger = [];
    }

    addIndexFingerSample(sample){
        this.index_finger.push(sample);
    }

    addMiddleFingerSample(sample){
        this.middle_finger.push(sample);
    }

    generateFullHand(){
        let id = this.id;
        let index_finger = this.index_finger;
        let middle_finger = this.middle_finger;
        return JSON.stringify({id, index_finger, middle_finger});
    }
    generateFullHand2(){
        let index_finger = this.index_finger;
        let middle_finger = this.middle_finger;
        return JSON.stringify({index_finger, middle_finger});
    }
}

let myReader = new Reader();

function beginEnrollment(){
    setReaderSelectField("enrollReaderSelect");
    myReader.setStatusField("enrollmentStatusField");
}

function beginIdentification(){
    setReaderSelectField("verifyReaderSelect");
    myReader.setStatusField("verifyIdentityStatusField");
    captureForIdentify();
}

function setReaderSelectField(fieldName){
    myReader.readerSelectField(fieldName);
    myReader.displayReader();
}

function showMessage(message, message_type="error"){
    let types = new Map();
    types.set("success", "my-text7 my-pri-color text-bold");
    types.set("error", "text-danger");
    let statusFieldID = myReader.currentStatusField;
    if(statusFieldID){
        let statusField = document.getElementById(statusFieldID);
        statusField.innerHTML = `<p class="my-text7 my-pri-color my-3 ${types.get(message_type)} font-weight-bold">${message}</p>`;
    }
}

function beginCapture(){
    if(!readyForEnroll()){
        return;
    }
    myReader.currentHand = new Hand();
    storeUserID();  // for current user in Hand instance
    myReader.reader.startCapture();
    showNextNotEnrolledItem();
}

function captureForIdentify() {
    if(!readyForIdentify()){
        return;
    }
    myReader.currentHand = new Hand();
    storeUserID();
    myReader.reader.startCapture();
    showNextNotEnrolledItem();
}

/**
 * @returns {boolean}
 */
function readyForEnroll(){
    return ((document.getElementById("userID").value !== "") && (document.getElementById("enrollReaderSelect").value !== "Select Fingerprint Reader"));
}

/**
* @returns {boolean}
*/
function readyForIdentify() {
    return document.getElementById("verifyReaderSelect").value !== "Select Fingerprint Reader";
}


function clearCapture(){
    clearInputs();
    clearPrints();
    clearHand();
    myReader.reader.stopCapture();
    document.getElementById("userDetails").innerHTML = "";
}

function clearInputs(){
    document.getElementById("userID").value = "";
    //document.getElementById("userIDVerify").value = "";
    //let id = myReader.selectFieldID;
    //let selectField = document.getElementById(id);
    //selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
}

function clearPrints(){
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");
    let verifyFingers = document.getElementById("verificationFingers");

    if (indexFingers){
        for(let indexfingerElement of indexFingers.children){
            indexfingerElement.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (middleFingers){
        for(let middlefingerElement of middleFingers.children){
            middlefingerElement.innerHTML = `<span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (verifyFingers){
        for(let finger of verifyFingers.children){
            finger.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }
}

function clearHand(){
    myReader.currentHand = null;
}

function showSampleCaptured(){
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if(nextElementID.startsWith("index") || nextElementID.startsWith("verification")){
        markup = `<span class="icon icon-indexfinger-enrolled" title="enrolled"></span>`;
    }

    if(nextElementID.startsWith("middle")){
        markup = `<span class="icon icon-middlefinger-enrolled" title="enrolled"></span>`;
    }

    if(nextElementID !== "" && markup){
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

function showNextNotEnrolledItem(){
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if(nextElementID.startsWith("index") || nextElementID.startsWith("verification")){
        markup = `<span class="icon capture-indexfinger" title="not_enrolled"></span>`;
    }

    if(nextElementID.startsWith("middle")){
        markup = `<span class="icon capture-middlefinger" title="not_enrolled"></span>`;
    }

    if(nextElementID !== "" && markup){
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

/**
 * @returns {string}
 */
function getNextNotEnrolledID(){
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");
    let verifyFingers = document.getElementById("verificationFingers");

    let enrollUserId = document.getElementById("userID").value;
    let verifyUserId = "ferreira";

    let indexFingerElement = findElementNotEnrolled(indexFingers);
    let middleFingerElement = findElementNotEnrolled(middleFingers);
    let verifyFingerElement = findElementNotEnrolled(verifyFingers);

    //assumption is that we will always start with
    //indexfinger and run down to middlefinger
    if (indexFingerElement !== null && enrollUserId !== ""){
        return indexFingerElement.id;
    }

    if (middleFingerElement !== null && enrollUserId !== ""){
        return middleFingerElement.id;
    }

    if (verifyFingerElement !== null && verifyUserId !== ""){
        return verifyFingerElement.id;
    }

    return "";
}

/**
 * 
 * @param {Element} element
 * @returns {Element}
 */
function findElementNotEnrolled(element){
    if (element){
        for(let fingerElement of element.children){
            if(fingerElement.firstElementChild.title === "not_enrolled"){
                return fingerElement;
            }
        }
    }

    return null;
}

function storeUserID(){
    let enrollUserId = document.getElementById("userID").value;
    let identifyUserId = "ferreira";
    myReader.currentHand.id = enrollUserId !== "" ? enrollUserId : identifyUserId;
}

function storeSample(sample){
    let samples = JSON.parse(sample.samples);
    let sampleData = samples[0].Data;

    let nextElementID = getNextNotEnrolledID();

    if(nextElementID.startsWith("index") || nextElementID.startsWith("verification")){
        myReader.currentHand.addIndexFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnrolledItem();
        return;
    }

    if(nextElementID.startsWith("middle")){
        myReader.currentHand.addMiddleFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnrolledItem();
    }
}

function serverEnroll(){
    if(!readyForEnroll()){
        return;
    }

    let data = myReader.currentHand.generateFullHand();
    let successMessage = "Enrollment Successful!";
    let failedMessage = "Enrollment Failed!";
    let payload = `data=${data}`;

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){
        if(this.readyState === 4 && this.status === 200){
            if(this.responseText === "success"){
                showMessage(successMessage, "success");
            }
            else{
                showMessage(`${failedMessage} ${this.responseText}`);
            }
        }
    };

    xhttp.open("POST", "../core/enroll.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(payload);
}

function serverIdentify() {
    if(!readyForIdentify()){
        return;
    }

    let data = myReader.currentHand.generateFullHand2();
    let detailElement = document.getElementById("userDetails");
    let successMessage = "Identification Successful!";
    let failedMessage = "Identification Failed!. Try again";
    let payload = `data=${data}`;

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200){
            if(this.responseText !== null && this.responseText !== ""){
                let response = JSON.parse(this.responseText);
                if(response !== "failed" && response !== null){
                    showMessage(successMessage, "success");
                    let currentDateTime = new Date().toLocaleString("es-MX", { timeZone: "America/Mexico_City" });
                    detailElement.innerHTML = `<div class="col text-center">
                                <label for="fullname" class="my-text7 my-pri-color">Nombre completo</label>
                                <input type="text" id="fullname" class="form-control" value="${response[0].fullname}" readonly>
                            </div>
                            <div class="col text-center">
                                <label for="email" class="my-text7 my-pri-color">Hora de captura</label>
                                <input type="text" id="email" class="form-control" value="${currentDateTime}" readonly>
                            </div>`;
                }
                else {
                    showMessage(failedMessage);
                    alert('Vuelva a poner el dedo')
                }
            }
        }
    };

    xhttp.open("POST", "../core/verify.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(payload);
}


window.onload = function() {
    cargarOpciones();
    //beginIdentification();
    //beginEnrollment();
    
};


function cargarOpciones() {
    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            try {
                const data = JSON.parse(this.responseText);
                const selector1 = document.getElementById('userID');
                const selector3 = document.getElementById('opcionSelector'); // Selector original

                data.forEach(opcion => {
                    // Para selector1
                    if (selector1) {
                        let option1 = document.createElement('option');
                        option1.value = opcion.username;
                        option1.text = opcion.username;
                        selector1.appendChild(option1);
                    }
                    // Para selector3
                    if (selector3) {
                        let option3 = document.createElement('option');
                        option3.value = opcion.username;
                        option3.text = opcion.username;
                        selector3.appendChild(option3);
                    }
                });
            } catch (error) {
                console.error('Error:', error);
            }
        } else if (this.readyState === 4) {
            console.error('Error al cargar opciones: ' + this.status);
        }
    };

    xhttp.open("GET", "../core/users.php", true);
    xhttp.send();
}

function traerRegistros() {
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
    let empleadoSeleccionado = document.getElementById('opcionSelector').value;

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            mostrarRegistrosEnTabla(JSON.parse(this.responseText));
        }
    };
    xhttp.open("POST", "../core/registros.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("fechaInicio=" + fechaInicio + "&fechaFin=" + fechaFin + "&empleado=" + empleadoSeleccionado);
}
function formatoFechaHora(fechaHoraStr) {
    let fechaHora = new Date(fechaHoraStr);
    let opciones = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true };
    return fechaHora.toLocaleString('es-MX', opciones);
}

function mostrarRegistrosEnTabla(datos) {
    if (datos.error) {
        document.getElementById("tablaRegistros").innerHTML = "<p>" + datos.error + "</p>";
    } else {
        let tabla = "<table class='table'><tr><th>Usuario</th><th>Hora de Entrada</th><th>Hora de Salida</th></tr>";
        datos.forEach(function(registro) {
            let entradaFormateada = formatoFechaHora(registro.Entrada);
            let salidaFormateada = formatoFechaHora(registro.Salida);
            tabla += "<tr><td>" + registro.Usuario + "</td><td>" + entradaFormateada + "</td><td>" + salidaFormateada + "</td></tr>";
        });
        tabla += "</table>";
        document.getElementById("tablaRegistros").innerHTML = tabla;
    }
}

function manejarLogin() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    if (username && password) {
        let xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var respuesta = JSON.parse(this.responseText);
                if (respuesta.success) {
                    window.location.href = 'src/html/home.php';
                } else if (respuesta) {
                    alert(respuesta);
                }
            }
        };

        xhttp.open("POST", "src/core/logearse.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
    } else {
        alert('Por favor, introduce usuario y contraseña');
    }

    return false;
}


function cambiarPassword() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    if (username && password) {
        let xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var respuesta = this.responseText;
                console.log(respuesta);
                alert(respuesta); 
            }
        };

        xhttp.open("POST", "../core/cambiarPassword.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhttp.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
    } else {
        alert('Por favor, introduce el nombre de usuario y la nueva contraseña');
    }

    return false;
}

function cerrarSesion() {
    window.location.href = '../core/cerrarSesion.php';
    session_destroy();

}
