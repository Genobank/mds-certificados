<?php
////////////////////////////////////////////////////////////////////////////////
// Ejecute esta consulta antes de utilizar este script.
//
// ALTER TABLE resultados ADD COLUMN url VARCHAR(1024) NULL;
////////////////////////////////////////////////////////////////////////////////

// Configuración de la aplicación //////////////////////////////////////////////
require __DIR__ . '/GenoBankioCertificado.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('GENOBANKIO_LA_RED', '--test'); // --test o --production
define('GENOBANKIO_FRASE_DE_DOCE_PALABRAS', 'wrong outside clever wagon father insane boy junk punch duck drift cupboard');
define('GENOBANKIO_PERMISO_ID', '41');

echo '<html><body>';

// GET parámetros //////////////////////////////////////////////////////////////
if (empty($_GET['folio'])) {
    die('URL invalida');
}
$folioQuery = $_GET['folio'] ?? null;
$lang = $_GET['lang'] ?? null;

// Obtener de la base de datos /////////////////////////////////////////////////
$dbdata = new Database();
$conn = $dbdata->connect();
if (!$conn) {
    die('Falló la conexión a la base de datos: ' . htmlspecialchars($conn->connect_error));
}
$sql = <<<SQL
SELECT CONCAT(nombre, ' ',apellidos) nombre_completo
     , u.id folio
     , u.edad edad
     , u.sexo sexo
     , u.nacionalidad nacionalidad
     , u.correo correo
     , r.antigeno antigeno
     , r.fecha_hora fecha_hora
     , r.metodo metodo
     , r.oxigenacion oxigenacion
     , r.temperatura temperatura
  FROM usuarios u
  JOIN resultados r
    ON r.id_usuario = u.id
 WHERE u.id = ?
SQL;
$statement = $conn->prepare($sql);
$statement->bind_param('i', $folioQuery);
$result = $statement->get_result();
$fila = $result->fetch_object();

if (empty($result)) {
    die('El registro no existe');
}
$antigenoResultado = strtoupper($fila->antigeno);
if (strcmp($antigenoResultado, 'POSITIVE') == 0) {
    $antigenoResultado = 'P';
} elseif (strcmp($antigenoResultado, 'NEGATIVE') == 0) {
    $antigenoResultado = 'N';
} else {
    die('Resultado de prueba no válido');
}
$fechaHora = date_create_from_format('F j, Y, g:i a', $fila->fecha_hora, new DateTimeZone('America/Mexico_City'));

// Cree un certificado si es necesario /////////////////////////////////////////
if (empty($fila->url)) {
    echo '<h1>Generando Certificado Blockchain / Generating Blockchain Certificate</h1>';
    echo '<p>Unos 20 segundos / about 20 seconds...</p>';
    $genobankio = new GenoBankioCertificado();
    $fila->url = $genobankio->crearCertificado(
        GENOBANKIO_LA_RED,                 // laRed                  // network
        GENOBANKIO_FRASE_DE_DOCE_PALABRAS, // fraseDeDocePalabras    // TWELVE_WORD_PHRASE
        GENOBANKIO_PERMISO_ID,             // permisoId              // PERMITTEE_ID
        $fila->nombre_completo,            // nombreDelPaciente      // PATIENT_NAME
        implode(' ', [$fila->nacionalidad, $fila->sexo, $fila->edad]),
                                           // pasaporteDelPaciente   // PATIENT_PASSPORT
        '1', /* 1=PCR */                   // codigoDeProcedimiento  // PROCEDURE_CODE
        $antigenoResultado, /* P / N */    // codigoDeResultado      // RESULT_CODE
        $folioQuery,                       // resultadosDeSerie      // SERIAL
        date_format($fechaHora, 'U') . '000' // marcaDeTiempo (UNIX milisegundos)   // TIMESTAMP
    );

    $sql = 'UPDATE resultados SET url = ? WHERE id_usuario = ?';
    $statement = $conn->prepare($sql);
    $statement->bind_param('is', $folioQuery, $fila->url);
    $result = $statement->execute();
    if (empty($result)) {
        die('No se pudo actualizar la base de datos');
    }
}

// Certificado de salida ///////////////////////////////////////////////////////
echo '<h1>Accede a Tu Certificado / Access Your Certificate</h1>';
echo '<p><a style="word-break:break-all" target="_blank" href="' . htmlspecialchars($fila->url) . '">' . htmlspecialchars($fila->url) . '</a></p>';

// Cierra la aplicación ////////////////////////////////////////////////////////
$conn->close();