<?php

class GenoBankioCertificado {
    const JAR = __DIR__ . '/genobankCertificates.jar';

    /**
     * @see https://github.com/Genobank/genobankj/blob/main/README.md
     * 
     * @param string $laRed                  // network
     * @param string $fraseDeDocePalabras    // TWELVE_WORD_PHRASE
     * @param string $permisoId              // PERMITTEE_ID
     * @param string $nombreDelPaciente      // PATIENT_NAME
     * @param string $pasaporteDelPaciente   // PATIENT_PASSPORT
     * @param string $codigoDeProcedimiento  // PROCEDURE_CODE
     * @param string $codigoDeResultado      // RESULT_CODE
     * @param string $resultadosDeSerie      // SERIAL
     * @param string $marcaDeTiempo (UNIX milisegundos)   // TIMESTAMP
     * @return void 
     */
    public function crearCertificado(
        string $laRed,
        string $fraseDeDocePalabras,
        string $permisoId,
        string $nombreDelPaciente,
        string $pasaporteDelPaciente,
        string $codigoDeProcedimiento,
        string $codigoDeResultado,
        string $resultadosDeSerie,
        string $marcaDeTiempo
    ) {
        $laRed = $laRed === '--production' ? '--production' : '--test';
        //$fraseDeDocePalabras = $fraseDeDocePalabras;
        $permisoId = intval($permisoId);
        $nombreDelPaciente = preg_replace(
            '/[^A-Za-z0-9 .-]/',
            '',
            iconv('UTF-8', 'ASCII//TRANSLIT', $nombreDelPaciente)
        );
        $pasaporteDelPaciente = preg_replace(
            '/[^A-Z0-9 -]/',
            '',
            iconv('UTF-8', 'ASCII//TRANSLIT', $pasaporteDelPaciente)
        );
        if ($codigoDeProcedimiento != 1) {
            die('Solo se acepta el código de procedimiento 1');
        }
        if (!in_array($codigoDeResultado, ['N', 'P'])) {
            die('Solo se aceptan resultados positivos y negativos');
        }
        $resultadosDeSerie = preg_replace(
            '/[^A-Z0-9 -]/',
            '',
            iconv('UTF-8', 'ASCII//TRANSLIT', $resultadosDeSerie)
        );
        if ($marcaDeTiempo != intval($marcaDeTiempo)) {
            die('El tiempo debe ser (milisegundos) de marca de tiempo UNIX');
        }
        if (intval($marcaDeTiempo) < 1577854800000) {
            die('El tiempo debe ser (milisegundos) de marca de tiempo UNIX');
        }

        $comandoDeShell = [
            'java',
            '-jar',
            self::JAR,
            escapeshellarg($laRed),
            escapeshellarg($fraseDeDocePalabras),
            escapeshellarg($permisoId),
            escapeshellarg($nombreDelPaciente),
            escapeshellarg($pasaporteDelPaciente),
            escapeshellarg($codigoDeProcedimiento),
            escapeshellarg($codigoDeResultado),
            escapeshellarg($resultadosDeSerie),
            escapeshellarg($marcaDeTiempo)    
        ];

        // CONTRASEÑA PRIVADA DENTRO
        // echo implode(' ', $comandoDeShell);

        exec(implode(' ', $comandoDeShell), $output, $resultCode);
        if ($resultCode != 0) {
            die('No se pudo crear el certificado');
        }
        $resultado = implode('', $output);

        if (!preg_match('|https://|', $resultado)) {
            echo htmlspecialchars($resultado);
            echo "<br>";
            die('No se pudo crear el certificado');
        }
        return trim($resultado);
    }
}