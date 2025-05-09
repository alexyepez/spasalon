# 🧖‍♀️ SpaSalon - Sistema de Gestión  

**Aplicación web para administrar citas, terapeutas, membresías e inventario de un spa o centro de bienestar.**  

## 🚀 Características  
- **Agendamiento de citas** para clientes.  
- **Gestión de terapeutas** (horarios, servicios asignados).  
- **Membresías** (planes, renovaciones, beneficios).  
- **Inventario** (productos, proveedores, stock).  
- **Recordatorios automáticos** (correos/SMS).  

## 🛠 Tecnologías  
- **Backend**: PHP, MySQL.  
- **Frontend**: HTML, CSS, JavaScript.  
- **Herramientas**: Git, VS Code.  

## 📦 Instalación  
1. Clona el repositorio:  
   ```bash
   git clone git@github.com:alexyepez/spasalon.git

2. Importa la base de datos (spasalon.sql)

## Configuración de la Base de Datos
   - Instala WampServer para Windows.
   - Accede a phpMyAdmin (`http://localhost/phpmyadmin`).
   - Crea una base de datos llamada `spasalon`.
   - Importa el archivo `sql/database.sql` desde la pestaña **Importar** en phpMyAdmin.
   - Verifica que las tablas (`roles`, `usuarios`, `clientes`, etc.) se hayan creado.
   - Inserta un registro en la tabla `roles`:
      ```sql
         INSERT INTO roles (nombre) VALUES ('cliente');

3. Configura las variables de entorno en includes/database.php.

## Equipo
- Future Projects

📄 Licencia
MIT License.