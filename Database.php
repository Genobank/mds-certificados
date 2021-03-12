<?php

class Database {
    function connect() {
        return new MysqliMock();
    }
}

class MysqliMock {
    public $connect_error = '';

    function prepare($sql): MysqliStmtMock {
        return new MysqliStmtMock;
    }

    function close(): bool {
        return true;
    }
}

class MysqliStmtMock {
    function bind_param() {
        return true;
    }

    function execute() {
        return true;
    }

    function get_result(): MysqliResultMock {
        return new MysqliResultMock;
    }
}

class MysqliResultMock {
    private $numRowsSent = 0;

    public $num_rows = 1;


    function fetch_object() {
        if ($this->numRowsSent > 0) {
            return false;
        }
        $this->numRowsSent++;
        return (object)[
            'nombre_completo'=>'HAKTAN TUNGER',
        	'folio'=>'99',
            'edad'=>'54',
            'sexo'=>'MASCULINO / MALE',
            'nacionalidad'=>'TURKEY',
            'correo'=>'haktant4@hotmail.com',
            'antigeno'=>'NEGATIVE',
            'fecha_hora'=>'January 30, 2021, 7:31 pm', // F j, Y, g:i a
            'metodo'=>'Inmunocromatografía / Immunochromatography',
            'oxigenacion'=>'95',
            'temperatura'=>'36.4'
        ];
    } 

    function fetch_assoc() {
        if ($this->numRowsSent > 0) {
            return false;
        }
        $this->numRowsSent++;
        return [
            'nombre_completo'=>'HAKTAN TUNGER',
        	'folio'=>'99',
            'edad'=>'54',
            'sexo'=>'MASCULINO / MALE',
            'nacionalidad'=>'TURKEY',
            'correo'=>'haktant4@hotmail.com',
            'antigeno'=>'NEGATIVE',
            'fecha_hora'=>'January 30, 2021, 7:31 pm', // F j, Y, g:i a
            'metodo'=>'Inmunocromatografía / Immunochromatography',
            'oxigenacion'=>'95',
            'temperatura'=>'36.4'
        ];
    } 
}