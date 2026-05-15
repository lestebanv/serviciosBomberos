# serviciosBomberos

![GitHub stars](https://img.shields.io/github/stars/lestebanv/serviciosBomberos?style=for-the-badge&logo=github) ![GitHub forks](https://img.shields.io/github/forks/lestebanv/serviciosBomberos?style=for-the-badge&logo=github) ![GitHub issues](https://img.shields.io/github/issues/lestebanv/serviciosBomberos?style=for-the-badge&logo=github)

## 📑 Tabla de Contenido

- [Descripción](#descripción)
- [Inicio Rápido](#inicio-rápido)
- [Capturas de Pantalla](#capturas-de-pantalla)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Contribuciones](#contribuciones)

## 📝 Descripción

serviciosBomberos es un plugin especializado para WordPress diseñado para modernizar y optimizar el proceso de solicitud de servicios en los cuerpos de bomberos. Funciona como un puente digital entre la estación de bomberos y la comunidad, ofreciendo una plataforma dedicada para que las empresas soliciten inspecciones técnicas obligatorias y para que los ciudadanos se inscriban en programas profesionales de capacitación en seguridad.

Al centralizar estos servicios esenciales dentro de una interfaz fácil de usar en WordPress, el plugin mejora la eficiencia organizacional, agiliza los tiempos de respuesta en tareas administrativas y garantiza que las iniciativas de cumplimiento de seguridad y formación educativa sean más accesibles para el público.

## ⚡ Inicio Rápido

```bash

# Clonar el repositorio
git clone https://github.com/lestebanv/serviciosBomberos.git

# Instalar dependencias y ejecutar

# (Ver configuración de desarrollo más abajo)
```

## 📸 Capturas de Pantalla

> **Tip:** Puedes generar automáticamente una imagen profesional del proyecto usando el botón **Screenshot** de arriba.

<p align="center">
  <img src="https://via.placeholder.com/800x400?text=Vista+Principal+de+la+Aplicacion" alt="Vista Principal de la Aplicación" width="80%"/>
</p>

<p align="center">
  <img src="https://via.placeholder.com/800x400?text=Demostracion+de+Funciones" alt="Demostración de Funciones" width="80%"/>
</p>

## 📁 Estructura del Proyecto

```plaintext
.
├── Requerimientos pendientes.txt
├── assets
│   ├── css
│   │   └── bomberos-styles.css
│   └── js
│       ├── bomberos-global.js
│       └── bomberos-main.js
├── bomberosServicios.php
├── includes
│   ├── activacion.php
│   ├── desactivacion.php
│   ├── insertarDemo.php
│   ├── shortcodes.php
│   └── utilidades.php
├── leame.txt
└── modulos
    ├── bomberos
    │   ├── claseControladorModulo.php
    │   ├── formularioCrearBombero.php
    │   ├── formularioEditarBombero.php
    │   ├── listadoBomberos.php
    │   └── manejadorEventos.js
    ├── cursos
    │   ├── claseControladorModulo.php
    │   ├── formularioCrearCurso.php
    │   ├── formularioEditarCurso.php
    │   ├── listadoCursos.php
    │   └── manejadorEventos.js
    ├── empresas
    │   ├── claseControladorModulo.php
    │   ├── formularioCrearEmpresa.php
    │   ├── formularioEditarEmpresa.php
    │   ├── listadoEmpresas.php
    │   └── manejadorEventos.js
    ├── inscripciones
    │   ├── claseControladorModulo.php
    │   ├── formularioEditarInscripcion.php
    │   ├── listadoInscripciones.php
    │   └── manejadorEventos.js
    ├── inspecciones
    │   ├── claseControladorModulo.php
    │   ├── formularioEditarInspeccion.php
    │   ├── listadoInspecciones.php
    │   └── manejadorEventos.js
    ├── pqr
    │   ├── claseControladorModulo.php
    │   ├── formularioEditarPqr.php
    │   ├── listadoPqr.php
    │   └── manejadorEventos.js
    └── publico
        ├── claseControladorModulo.php
        ├── registroInscripciones
        │   ├── claseControladorModulo.php
        │   ├── formularioInscripcionCurso.php
        │   ├── manejadorEventos.js
        │   └── mensajeRespuestaInscripcion.php
        ├── registroPqr
        │   ├── claseControladorModulo.php
        │   ├── confirmarRegistro.php
        │   ├── formPqr.php
        │   └── manejadorEventos.js
        ├── shortCodes-main.js
        └── solicitudInspecciones
            ├── claseControladorModulo.php
            ├── confirmarRegistro.php
            ├── frmBuscarEmpresa.php
            ├── frmRegistrarEmpresaSolicitud.php
            ├── frmRegistrarSoloSolicitud.php
            └── manejadorEventos.js
```

## 👥 Contribuciones

¡Las contribuciones son bienvenidas! Así puedes ayudar:

1. Haz un **Fork** del repositorio
2. Clona tu fork:
   ```bash
   git clone https://github.com/lestebanv/serviciosBomberos.git
   ```

3. Crea una nueva rama:
   ```bash
   git checkout -b feature/tu-funcionalidad
   ```

4. Guarda tus cambios:
   ```bash
   git commit -am 'Agregar nueva funcionalidad'
   ```

5. Sube tus cambios:
   ```bash
   git push origin feature/tu-funcionalidad
   ```

6. Abre un **Pull Request**
