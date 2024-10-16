# Laravel API para subida de archivo de 1 millón de registros

Este proyecto consiste en la creación de una API desarrollada en Laravel, con el objetivo de gestionar y procesar archivos de gran tamaño de manera eficiente. En este caso específico, se implementó la funcionalidad para subir y procesar un archivo de 1 millón de registros, optimizando el uso de recursos del servidor y asegurando que el sistema no experimente problemas de memoria durante el proceso.

### Características clave

- **Subida de archivos pesados:** Se habilitó la posibilidad de subir archivos grandes mediante el uso de controladores personalizados y el manejo eficiente de solicitudes de archivos en Laravel, utilizando técnicas como la validación de tamaño máximo y tipos de archivo permitidos.
- **Extracción y validación eficiente de información:** Para evitar el consumo excesivo de memoria durante la extracción y validación de la información, se implementó un enfoque basado en el uso de lectura de archivos por lotes (batch processing). Esto permite procesar el archivo en pequeñas partes en lugar de cargar todo el archivo en memoria. Además, cada lote de datos es validado utilizando reglas específicas que aseguran la integridad y consistencia de los datos.
- **Carga optimizada a MySQL:** Una vez validados los datos, se insertan en la base de datos MySQL de manera optimizada utilizando el comando de inserciones en bloque (bulk insert). Esto permite que múltiples registros sean ingresados de una sola vez, reduciendo así la cantidad de operaciones de escritura y mejorando la eficiencia del proceso.
- **Manejo de colas (queues):** Para mejorar aún más la performance y evitar la sobrecarga del servidor durante el procesamiento masivo, se implementaron colas (queues) de Laravel. Esto permite procesar los registros de manera asíncrona en segundo plano, distribuyendo la carga de trabajo y evitando el bloqueo de otros procesos importantes del sistema.

### Beneficios

- **Uso eficiente de la memoria:** El uso de técnicas como la lectura por lotes y la inserción en bloque permite que la aplicación mantenga un consumo de memoria bajo incluso al procesar archivos de gran tamaño.
- **Escalabilidad:** Gracias a la implementación de colas, el sistema es capaz de manejar grandes volúmenes de datos sin impactar negativamente en el rendimiento general del servidor.
- **Fiabilidad y validación:** Los datos se validan en tiempo real durante el proceso de extracción, garantizando que solo se carguen registros válidos en la base de datos.

Este enfoque asegura que el sistema pueda manejar grandes volúmenes de datos de manera eficiente y segura, incluso en servidores con limitaciones de memoria.

## Requisitos previos

* Instalación de XAMPP (es el que utilizo para el proyecto).
* Abrir el archivo `php.ini` y modificar los parámetros `upload_max_filesize` y `post_max_size` a 100M cada uno. Esto para que no marque error al mandar la petición de un archivo que pese mucho.
* Al clonar o descargar el proyecto, hay que ubicarlo dentro de la carpeta `htdocs`. No se usará el comando `php artisan serve` para levantar el servidor, sino que mandaramos directamente la petición a la carpeta `public` del proyecto para que respete la configuración del `php.ini` que se modificó. En estas pruebas, creé una carpeta extra dentro de `htdocs` llamada `Proyectos` y ahí dentro puse el repositorio (como se muestra la imagen a continuación):
![Ubicacion proyecto](/public/readme/ubicacion_proyecto.png "Ubicación proyecto")
* Crear un archivo .csv de 4 columnas con las siguientes características:
  * **Columna 1:** Un número random entre 1 y 10,000. Esta columna figura el ID del usuario que estará relacionado el registro.
  * **Columna 2:** Una fecha en formato `YYYY-MM-DD HH-MM-SS`.
  * **Columna 3:** Un string que haga referencoa a una acción dentro de algún sistema.
  * **Columna 4:** Un string que haga referencia al path visitado.

El archivo que cargué para la prueba es así:
![archivo](/public/readme/preview_archivo.png "Preview Archivo")

## Instalación del proyecto

1. Clonar el repositorio.
2. Ejecutar el comando `composer install`.
3. Copiar y pegar el archivo `.env.example` y renombrarlo a `.env`
4. Crear una base de datos en MySQL llamada `laravel_million_records`.
5. En la parte de la información de la base de datos en el archivo `.env`, dejarlo de la siguiente manera:
![ENV DB](/public/readme/env_db.png "ENV DB")
Nota: El **username** y **password** debe de ser el que tengas configurado en tu computadora. 
6. Ejecutar el comando `php artisan migrate --seed` para ejecutar las migraciones y un seeder que crea 10k registros de usuarios para las pruebas.
7. Iniciar el servidor de `Apache` y `MySQL` del programa `XAMPP`.
8. Descargar el [archivo](/public/readme/endpoints.postman_collection.json) e importar el json en el programa [Postman](https://www.postman.com/).
9. Abrir el endpoint llamado `Registro de nuevo usuario` y enviar la petición. Si todo salió bien, se obtendrá una respuesta `204 No Content`.
10. Abrir el endpoint llamado `Login` y con las mismas credenciales `email` y `password` enviadas en el endpoint anterior, enviar la petición. Si todo salió bien, se obtendrá una respuesta parecida a la siguiente:
![login](/public/readme/login_postman.png "Login")
11. Copiar el `token` de la petición anterior.
12. Abrir el endpoint llamado `Enviar archivo`. En la parte de `Authorization` poner el token copiado anteriormente. En el body cargar el archivo .csv y enviar la petición. Si todo salió bien, se tendrá una respuesta parecida a la siguiente:
![enviar_archivo](/public/readme/enviar_archivo.png "Enviar archivo")
13. Abrir el endpoint llamado `Estatus de la carga`. En la parte de `Authorization` poner el token copiado anteriormente. En la URL cambiar el valor del ID del batch por el de la respuesta del endpoint anterior. Enviar la petición y si todo salió bien, se tendrá una respuesta parecida a la siguiente:
![batch_info](/public/readme/batch_info.png "Información del batch")
14. Para ejecutar los jobs registrados en la base de datos, se tiene que ejecutar el comando `php artisan queue:work` dentro del proyecto. Una vez ejecutado se empezarán a ejecutar los jobs. Para revisar el estatus de estos, debes volver a ejecutar el endpoint `Estatus de la carga` para ver la información de todo el proceso.
