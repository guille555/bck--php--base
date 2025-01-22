# bck--php--base
Este proyecto, consiste en una API REST donde se pueden registrar usuarios y asignarles un grupo. Los grupos a su ves poseen menús a los que el usuario puede acceder.
El proyecto se separa en capas. La capa "controlador" que expone las rutas de acceso para los clientes. La capa "servicio" donde se procesa los datos de las peticiones y se produce la información a ser retornada al cliente. La capa "dao" provee funciones que interactuan con la base de datos.

Tecnologías utilizadas:

  * PHP 8.2
  * MySQL 8
