<!DOCTYPE html>
<html lang="es">
<head>
    <title>BaseDatos :: PruebasUsabilidad</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Rocío Cenador Martínez" /> 
    <link href="Ejercicio6.css" rel="stylesheet" />
</head>   
<body>
    <header>
        <h1>Operaciones con bases de datos y MySQLi</h1>
    </header>    
    <nav>
        <h2>Menú:</h2>
        <a href="crearBaseDatos.html">Crear base de datos</a>
        <a href="crearTabla.html">Crear tabla</a>
        <a href="insertarDatos.html">Insertar datos en la tabla</a>
        <a href="buscarDatos.html">Buscar datos en la tabla</a>
        <a href="modificarDatos.html">Modificar datos en la tabla</a>
        <a href="eliminarDatos.html">Eliminar datos de la tabla</a>
        <a href="vaciarTabla.html">Vaciar tabla</a>
        <a href="generarInforme.html">Generar Informe</a>
        <a href="cargarDatos.html">Cargar datos desde archivo csv</a>
        <a href="exportarDatos.html">Exportar datos a un archivo csv</a>
    </nav>  
    <main>
        <?php

            class BaseDatos{

                public function __construct(){
                    // Usuario ya creado en la base de datos MySQL en XAMPP: MySQL [Admin]
                    //datos de la base de datos
                    $this->servername = "localhost";
                    $this->username = "DBUSER2020";
                    $this->password = "DBPSWD2020";

                    // Conexión al SGBD local con XAMPP con el usuario creado 
                    $this->db = new mysqli($this->servername,$this->username,$this->password);

                    //comprobamos conexión
                    if($this->db->connect_error) {
                        exit ("<p>ERROR de conexión:".$this->db->connect_error."</p>");  
                    } else {echo "<p>Conexión establecida con " . $this->db->host_info . "</p>";}

                }

                public function creaBaseDatos($nombreBaseDatos){

                    try{
                        //prepara la sentencia de creación
                        $consultaPre = $this->db->prepare( "CREATE DATABASE IF NOT EXISTS " . $nombreBaseDatos . " COLLATE utf8_spanish_ci");   
                                        
                    } catch (Exception $e){
                        die('<p>Error preparando consulta: ' .  $e->getMessage() . '</p>');
                    }  

                    //Ejecuta la sentencia preparada y comprueba su estado
                    if($consultaPre->execute() === TRUE){
                        echo "<p>Base de datos " . $nombreBaseDatos . " creada con éxito</p>";
                    } else { 
                        echo "<p>ERROR en la creación de la Base de Datos " . $nombreBaseDatos . ". Error: " . $this->db->error . "</p>";
                    }
                    $consultaPre->close();

                    $this->cerrarConexion();
                }

                /**
                 * Crear la tabla PruebasUsabilidad
                 */
                public function creaTabla($nombreTabla, $baseDatos){

                    //selecciono la base de datos BaseDatos para utilizarla
                    $this->db->select_db($baseDatos);
                    
                    /** Datos de la persona que realiza la prueba
                    * Nombre, Apellidos, E-mail, Teléfono, Edad, Sexo
                    * Nivel o pericia informática de 0 a 10
                    * Tiempo en segundos que ha llevado la tarea
                    * Si la tarea la ha realizado correctamente o no //0 para False, 1 para True
                    * Comentarios sobre problemas encontrados al usar la aplicación
                    * Propuestas de mejora de la aplicación
                    * Valoración de la aplicación por parte del usuario de 0 a 10
                     */
                    $crearTabla = "CREATE TABLE IF NOT EXISTS $nombreTabla (id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
                    nombre VARCHAR(255) NOT NULL, 
                    apellidos VARCHAR(255) NOT NULL, 
                    dni VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    telefono INT UNSIGNED NOT NULL,
                    edad TINYINT UNSIGNED NOT NULL,
                    sexo CHAR NOT NULL CHECK(sexo in ('H', 'M')),
                    pericia TINYINT NOT NULL CHECK(pericia BETWEEN 0 AND 10),
                    tiempo INT NOT NULL,
                    aprobado BIT NOT NULL,
                    comentarios TEXT,
                    mejoras TEXT,
                    valoracion TINYINT NOT NULL CHECK(valoracion BETWEEN 0 AND 10),
                    PRIMARY KEY (id))";

                    //Crea la tabla. No se necesita sentencia de preparación porque los metadatos son internos
                    if($this->db->query($crearTabla) === TRUE){
                        echo "<p>Tabla ". $nombreTabla . " creada con éxito </p>";
                    } else { 
                        echo "<p>ERROR en la creación de la tabla ". $nombreTabla .". Error : ". $this->db->error . "</p>";
                        exit();
                    }
                    
                $this->cerrarConexion();
            }

                public function insertaDatos(){

                    //Selecciona la base de datos
                    $this->db->select_db("BaseDatos");
                    
                    try{
                        //prepara la sentencia de inserción
                        $consultaPre = $this->db->prepare("INSERT INTO PruebasUsabilidad (nombre, apellidos, dni, email, telefono, edad, sexo, pericia, tiempo, aprobado, comentarios, mejoras, valoracion) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");                   
                    } catch (Exception $e){
                        echo '<p>Error preparando consulta: ' .  $e->getMessage() . '</p>';
                    }   
                
                    //Comprobación de campos:
                    if(!($_POST["sexo"]=='H' | $_POST["sexo"]=='M')){
                        echo "<p>El campo sexo debe contener 'H' o 'M' (Hombre o Mujer)</p>";
                        exit();
                    }
                    if($_POST["nombre"] == NULL){
                        echo "<p>El campo nombre no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["apellidos"] == NULL) {
                        echo "<p>El campo apellidos no puede ser nulo</p>";
                        exit();
                    }
                    
                    if($_POST["dni"] == NULL){
                        echo "<p>El campo DNI no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["email"] == NULL){
                        echo "<p>El campo email no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["telefono"] == NULL){
                        echo "<p>El campo Teléfono no puede ser nulo</p>";
                        exit();
                    }
                    if($_POST["edad"] == NULL){
                        echo "<p>El campo Edad no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["sexo"] == NULL){
                        echo "<p>El campo Sexo no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["tiempo"] == NULL){
                        echo "<p>El campo Tiempo no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["aprobado"] == NULL){
                        echo "<p>El campo Resultado no puede ser nulo</p>";
                        exit();
                    }    

                    //añade los parámetros de la variable Predefinida $_POST
                    $consultaPre->bind_param('ssisiisiiissi', 
                        $_POST["nombre"], $_POST["apellidos"], $_POST["dni"], $_POST["email"], 
                        $_POST["telefono"], $_POST["edad"], $_POST["sexo"], $_POST["pericia"],$_POST["tiempo"], 
                        $_POST["aprobado"], $_POST["comentarios"], $_POST["mejoras"], $_POST["valoracion"]);    

                    try{
                        //ejecuta la sentencia
                        $consultaPre->execute();
                        echo '<p>Fila agregada con éxito: ' .  'Nombre: ' .  $_POST["nombre"] . ', Apellidos: ' . $_POST["apellidos"] . ', DNI: ' . $_POST["dni"] . '</p>';
                    } catch (Exception $e){
                        echo '<p>No se ha podido agregar la fila: ' .  $e->getMessage() . '</p>';
                    }

                }


                public function buscaDatos(){

                    $this->db->select_db("BaseDatos");

                    // prepara la consulta
                    $consultaPre = $this->db->prepare("SELECT * FROM PruebasUsabilidad WHERE id = ?");   

                    $consultaPre->bind_param('s', $_POST["id"]);    

                    $consultaPre->execute();

                    //Obtiene los resultados como un objeto de la clase mysqli_result
                    $resultado = $consultaPre->get_result();

                    //Visualización de los resultados de la búsqueda
                    if ($resultado->fetch_assoc()!= NULL) {
                        echo "<p>Las filas de la tabla 'pruebasUsabilidad' que coinciden con la búsqueda son:</p>";
                        $resultado->data_seek(0); //Se posiciona al inicio del resultado de búsqueda
                        while($fila = $resultado->fetch_assoc()) {
                            echo "<p>idRegistro: " . $fila["id"] . " Nombre: " . $fila["nombre"] . ", Apellidos: " . $fila["apellidos"] .  ", DNI: " . $fila["dni"]
                            . ", email: " . $fila["email"]. ", Teléfono: " .  $fila["telefono"] . ", Edad: " . $fila["edad"] . 
                            ", Sexo: " . $fila["sexo"] . ", Pericia: " .  $fila["pericia"] . ", Tiempo de la tarea: " . $fila["tiempo"] .
                            ", Resultado: " . $fila["aprobado"] . ", Comentarios: " . $fila["comentarios"] . ", Mejoras: " . $fila["mejoras"] . ", Valoración: " . $fila["valoracion"] . "</p>";
                        }            
                    } else {
                        echo "<p>Búsqueda sin resultados</p>";
                    }
                    $resultado->data_seek(0);
                    return $resultado;

                }

                public function vaciarTabla(){

                    $this->db->select_db("BaseDatos");

                    $vaciarLista = "DELETE FROM PruebasUsabilidad";

                    if($this->db->query($vaciarLista) !== TRUE){
                        echo "<p>ERROR para vaciar lista. Error : ". $this->db->error . "</p>";
                        exit();
                    } else {
                        echo "<p>Tabla vacía.</p>";

                    }
                }

                public function modificaDatos(){

                    $this->db->select_db("BaseDatos");

                    //Comprobar que la fila existe
                    try{
                        //prepara la sentencia de inserción
                        $resultado = $this->db->prepare("SELECT * FROM PruebasUsabilidad WHERE id = ?");                   
                    } catch (Exception $e){
                        echo '<p>Error preparando consulta: ' .  $e->getMessage() . '</p>';
                    } 

                    $resultado->bind_param('i', $_POST["id"]);    

                    try{
                        $resultado->execute();
                        $resultado = $resultado->get_result();
                    } catch (Exception $e){
                        echo '<p>No se ha podido realizar la consulta: ' .  $e->getMessage() . '</p>';
                    }
                    //Comprobar que la fila existe
                    if($resultado->fetch_assoc() == NULL) {
                        echo '<p>No hay registros con ese id, pruebe de nuevo con otro valor</p>';
                        exit;
                    }
                    
                    try{
                        //prepara la sentencia de inserción
                        $consultaPre = $this->db->prepare("UPDATE PruebasUsabilidad SET nombre=?, apellidos=?, dni=?, email=?, telefono=?, edad=?, sexo=?, pericia=?, tiempo=?, aprobado=?, comentarios=?, mejoras=?, valoracion=? WHERE dni = ?");                   
                    } catch (Exception $e){
                        echo '<p>Error preparando consulta: ' .  $e->getMessage() . '</p>';
                    }   
                
                    //Comprobación de campos:
                    if(!($_POST["sexo"]=='H' | $_POST["sexo"]=='M')){
                        echo "<p>El campo sexo debe contener 'H' o 'M' (Hombre o Mujer)</p>";
                        exit();
                    }
                    if($_POST["nombre"] == NULL){
                        echo "<p>El campo nombre no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["apellidos"] == NULL) {
                        echo "<p>El campo apellidos no puede ser nulo</p>";
                        exit();
                    }
                    
                    if($_POST["dni"] == NULL){
                        echo "<p>El campo DNI no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["email"] == NULL){
                        echo "<p>El campo email no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["telefono"] == NULL){
                        echo "<p>El campo Teléfono no puede ser nulo</p>";
                        exit();
                    }
                    if($_POST["edad"] == NULL){
                        echo "<p>El campo Edad no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["sexo"] == NULL){
                        echo "<p>El campo Sexo no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["tiempo"] == NULL){
                        echo "<p>El campo Tiempo no puede ser nulo</p>";
                        exit();
                    } 
                    if($_POST["aprobado"] == NULL){
                        echo "<p>El campo Resultado no puede ser nulo</p>";
                        exit();
                    }    

                    //añade los parámetros de la variable Predefinida $_POST
                    $consultaPre->bind_param('ssisiisiiissii', 
                        $_POST["nombre"], $_POST["apellidos"], $_POST["dni"], $_POST["email"], 
                        $_POST["telefono"], $_POST["edad"], $_POST["sexo"], $_POST["pericia"],$_POST["tiempo"], 
                        $_POST["aprobado"], $_POST["comentarios"], $_POST["mejoras"], $_POST["valoracion"], $_POST["dni"]);    

                    try{
                        //ejecuta la sentencia
                        $consultaPre->execute();
                        echo '<p>Fila modificada con éxito: ' .  'Nombre: ' .  $_POST["nombre"] . ', Apellidos: ' . $_POST["apellidos"] . ', DNI: ' . $_POST["dni"] . '</p>';
                    } catch (Exception $e){
                        echo '<p>No se ha podido modificar la fila: ' .  $e->getMessage() . '</p>';
                    }

                }

                public function eliminaDatos(){

                    $resultado = $this->buscaDatos();

                    //Visualización de los resultados de la búsqueda
                    if ($resultado->fetch_assoc()!= NULL) {
                        echo "<p>Las filas de la tabla 'PruebasUsabilidad' que van a ser eliminadas son:</p>";
                        $resultado->data_seek(0); //Se posiciona al inicio del resultado de búsqueda
                        while($fila = $resultado->fetch_assoc()) {
                            echo "<p>" . "id = " . $fila["id"] . " / dni = " . $fila["dni"] . " / nombre = ".$fila['nombre']." / apellidos = ". $fila['apellidos'] . "</p>"; 
                        } 
                    echo "</ul>";

                    //Realiza el borrado
                    //prepara la sentencia SQL de borrado
                    $consultaPre = $this->db->prepare("DELETE FROM PruebasUsabilidad WHERE id = ?");   
                    //obtiene los parámetros de la variable almacenada
                    $consultaPre->bind_param('s', $_POST["id"]);    
                    $consultaPre->execute();
                    echo "<p>Eliminado con éxito</p>";

                    }
                }

                public function generaInforme(){
                    $edad = $this->media("edad");
                    $sexos = $this->porcentajeSexo();
                    $pericia = $this->media("pericia");
                    $tiempo = $this->media("tiempo");
                    $aprobado = $this->porcentajeAprobado();
                    $valoracion = $this->media("valoracion");
                    echo "<h2>Informe sobre los datos de la tabla: </h2>";
                    echo "<ul>";
                    echo "<li>Media de edad: " . $edad . "</li>";
                    echo "<li>Porcentaje de hombres: " . $sexos[0] . "%</li>";
                    echo "<li>Porcentaje de mujeres: " . $sexos[1] . "%</li>";
                    echo "<li>Nivel/pericia media: " . $pericia . "</li>";
                    echo "<li>Media de tiempo empleado: " . $tiempo . "</li>";
                    echo "<li>Porcentaje de aprobados: " . $aprobado . "%</li>";
                    echo "<li>Valoración media: " . $valoracion . "</li>";
                    echo "</ul>";

                }

                private function recuperaDatos(){
                    
                    $this->db->select_db("BaseDatos");

                    try{
                        $resultado = $this->db->query("SELECT * FROM PruebasUsabilidad");
                    } catch(Exception $e){
                        die('<p>Error recuperando datos de la tabla: ' .  $e->getMessage() . '</p>');
                    }
                    $datos = array();
                    while( $fila = $resultado->fetch_assoc() ) {
                        $datos[] = $fila;
                    }
                    return $datos;
                }
                
                public function importaDatos(){
               
                    //Nombre para el archivo
                    $file = 'pruebasUsabilidad.csv';
                    //Base de datos donde se localiza la tabla a modificar
                    $this->db->select_db("BaseDatos");

                    //Abrir archivo de lectura
                    $csv_file = fopen($file, 'r');

                    $i = 0; //Variable de control
                    while(($fila = fgetcsv($csv_file)) !== FALSE) {
                        if ($i != 0){ //para saltar la primera fila formada por los nombres de las columnas
                            
                            $fila = explode(";", $fila[0]); //$fila es un array con un solo objeto, una String formada por valores y separadores ;
                   
                            try{
                                //prepara la sentencia para importar de fuente externa
                                $consultaPre = $this->db->prepare("INSERT INTO PruebasUsabilidad (nombre, apellidos, dni, email, telefono, edad, sexo, pericia, tiempo, aprobado, comentarios, mejoras, valoracion) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");                   
                            } catch (Exception $e){
                                echo '<p>Error preparando consulta: ' .  $e->getMessage() . '</p>';
                            }
                        
                            //$fila[0] corresponde al ID (auto_increment)
                            $consultaPre->bind_param('ssisiisiiissi', 
                                $fila[1],$fila[2],$fila[3],$fila[4], 
                                $fila[5], $fila[6],$fila[7], $fila[8],$fila[9], 
                                $fila[10],$fila[11], $fila[12],$fila[13]);    

                            try{
                               
                                $result = $consultaPre->execute();
                                echo "<p>Fila importada con éxito</p>";
                            } catch (Exception $e){
                                echo '<p>No se ha podido agregar la fila: ' .  $e->getMessage() . "</p>";
                            }
                        }
                        $i++;

                    } //endOfWHILE  

                    fclose($csv_file);
                }
                
                public function exportaDatos(){

                    $datos = $this->recuperaDatos();

                    //Nombre para el archivo
                    $file = 'pruebasUsabilidad.csv'; 
                    
                    //Crear el archivo con los metadatos de las columnas
                    try{
                        file_put_contents($file, "id; nombre; apellidos; dni; email; telefono; edad; sexo; pericia; tiempo; aprobado; comentarios; mejoras; valoracion " . PHP_EOL); 
                    } catch(Exception $e){
                        echo $e->get_message();
                    }
                    
                    for($i = 0; $i < count($datos); $i++)
                    {
                        //Escribir cada fila en el archivo como un único string con ;
                        file_put_contents($file, implode(";", $datos[$i]), FILE_APPEND); 
                        file_put_contents($file, PHP_EOL, FILE_APPEND); //Salto de línea
                    }

                    //Verificar la creación del archivo
                    if (file_exists($file)) {
                        header('Content-Disposition: attachment; filename='.basename($file));
                        header('Content-Length: ' . filesize($file));
                        ob_clean();
                        flush(); 
                        echo "<h2>Datos exportados: </h2>";
                        readfile($file);			
                    } else
                    {
                        echo "<p>Error al crear el archivo.</p>";
                    }
                }

                public function media($campo){
                    $this->db->select_db("BaseDatos");
                    
                    try{
                        $resultado =  $this->db->query(
                            "SELECT AVG(" . $campo . ") FROM PruebasUsabilidad");
                    } catch(Exception $e){
                        die('<p>Error ejecutando consulta: ' .  $e->getMessage() . '</p>');
                    }

                    return number_format($resultado->fetch_row()[0], 2);
                }               

                public function porcentajeSexo(){

                    $this->db->select_db("BaseDatos");
                    
                    try{
                        $hombres =  $this->db->query(
                            "SELECT COUNT(id) FROM PruebasUsabilidad WHERE sexo = 'H'");
                        $mujeres =  $this->db->query(
                            "SELECT COUNT(id) FROM PruebasUsabilidad WHERE sexo = 'M'");
                        $total = $this->db->query("SELECT count(id) FROM PruebasUsabilidad");
                    } catch(Exception $e){
                        die('<p>Error ejecutando consulta: ' .  $e->getMessage(). '</p>');
                    }

                    $hombres = $hombres->fetch_row()[0];
                    $mujeres = $mujeres->fetch_row()[0];
                    $total = $total->fetch_row()[0];

                    $hombres = number_format(($hombres / $total )*100, 2);
                    $mujeres = number_format(($mujeres / $total )*100, 2);

                    $totales[0] = $hombres;
                    $totales[1] = $mujeres;
                    return $totales;
                }

                public function porcentajeAprobado(){
                    
                    $this->db->select_db("BaseDatos");
                    
                    try{
                        $aprobados =  $this->db->query(
                            "SELECT COUNT(id) FROM PruebasUsabilidad WHERE aprobado = 1");
                        $total = $this->db->query("SELECT count(id) FROM PruebasUsabilidad");
                    } catch(Exception $e){
                        die('<p>Error ejecutando consulta: ' .  $e->getMessage(). '</p>');
                    }
                    $porcentaje = number_format(($aprobados->fetch_row()[0] / $total->fetch_row()[0])*100, 2);

                    return $porcentaje;

                }

                public function cerrarConexion(){
                    $this->db->close();
                }

                public function submit(){
                    if (count($_POST)>0) 
                    {   
                        // Llama a cada método del objeto $baseDatos 
                        // dependiendo de qué formulario ha hecho el submit
                        if(isset($_POST["submitBBDD"])) $this->creaBaseDatos("BaseDatos");
                        if(isset($_POST["submitTabla"])) $this->creaTabla("PruebasUsabilidad", "BaseDatos");
                        if(isset($_POST["submitInserta"])) $this->insertaDatos();

                        //Tarea 4
                        if(isset($_POST["buscar"])) $this->buscaDatos();
                        if(isset($_POST["eliminar"])) $this->eliminaDatos();
                        if(isset($_POST["modificar"])) $this->modificaDatos();
                       
                        
                        //Tarea 5
                        if(isset($_POST["mediaEdad"])) $this->media("edad");
                        if(isset($_POST["porcentajeSexos"])) $this->porcentajeSexo();
                        if(isset($_POST["mediaPericia"])) $this->media("pericia");
                        if(isset($_POST["mediaTiempo"])) $this->media("tiempo");
                        if(isset($_POST["porcentajeAprobados"])) $this->porcentajeAprobado();
                        if(isset($_POST["mediaValoración"])) $this->media("valoracion");
                        if(isset($_POST["generaInforme"])) $this->generaInforme();
                        if(isset($_POST["vaciar"])) $this->vaciarTabla();


                        //Tarea 6
                        if(isset($_POST["exportar"])) $this->exportaDatos();
                        //Tarea 7
                        if(isset($_POST["importar"])) $this->importaDatos();

                    }
                }
            }
            
            $baseDatos = new BaseDatos();
            $baseDatos->submit();
            
        ?> 
    </main>
</body>
</html>