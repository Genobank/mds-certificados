# MDS Certificados

Implementación de ejemplo para

- https://angomedical.com/covidtest/verificaResultados-f.php?lang=spa&folio=XXX
- https://angomedical.com/covidtest/generaPDF.php?lang=spa&folio=XXX

## Instalación

1. Ejecute esta consulta antes de utilizar este script.

   ```sql
   ALTER TABLE resultados ADD COLUMN url VARCHAR(1024) NULL;
   ```

2. Compile e instale Java JAR en https://github.com/genobank/genobankj

2. Copie el archivo JAR a la carpeta

   ```sh
   cp ~/Developer/genobankj/target/certificates-1.0-SNAPSHOT-jar-with-dependencies.jar ./genobankCertificates.jar
   ```

3. Copie archivos a su servidor web

   ```sh
   cp generaPDF.php GenoBankioCertificado.php genobankCertificates.jar /var/www/covidtest/
   ```

   * :information_source: Note: Database.php y demo.php son para propósitos de prueba.

4. Actualizar

   1. `GENOBANKIO_LA_RED`
   2. `GENOBANKIO_FRASE_DE_DOCE_PALABRAS`
   3. `GENOBANKIO_PERMISO_ID`

## Demostración

![ss1](/Users/williamentriken/Developer/mds-certificados/ss1.png)

![ss2](/Users/williamentriken/Developer/mds-certificados/ss2.png)