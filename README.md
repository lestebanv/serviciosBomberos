<h1 align="center"> lestebanv-serviciosBomberos </h1>

<p align="center"> 
  Una infraestructura digital integral y modular diseñada para optimizar los flujos administrativos, las solicitudes de servicios públicos y la supervisión operativa de los cuerpos de bomberos.
</p>

<p align="center">
  <img alt="Build" src="https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge">
  <img alt="Issues" src="https://img.shields.io/badge/Issues-0%20Open-blue?style=for-the-badge">
  <img alt="Contributions" src="https://img.shields.io/badge/Contributions-Welcome-orange?style=for-the-badge">
  <img alt="License" src="https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge">
</p>

<!-- 
  **Nota:** Estas son insignias estáticas de ejemplo. Reemplázalas con las insignias reales de tu proyecto.
  Puedes generar las tuyas en https://shields.io
-->

---

### 📖 Tabla de Contenido
- [📖 Tabla de Contenido](#-tabla-de-contenido)
- [🌟 Descripción General](#-descripción-general)
- [✨ Características Principales](#-características-principales)
- [🛠️ Tecnologías y Arquitectura](#️-tecnologías-y-arquitectura)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [🚀 Primeros Pasos](#-primeros-pasos)
- [🔧 Uso](#-uso)
- [🤝 Contribuciones](#-contribuciones)
- [📝 Licencia](#-licencia)

---

### 🌟 Descripción General

**lestebanv-serviciosBomberos** es una biblioteca de gestión especializada basada en PHP, desarrollada para digitalizar el complejo ecosistema de servicios de los cuerpos de bomberos. Al proporcionar un enfoque estructurado y modular para manejar desde la gestión del personal hasta las inspecciones de seguridad pública, este sistema actúa como la columna vertebral digital de las organizaciones de servicios de emergencia, garantizando integridad de datos, accesibilidad y eficiencia operativa.

> **El Problema:** Los cuerpos de bomberos modernos suelen enfrentar dificultades debido a la gestión fragmentada de datos. Las solicitudes de inspecciones técnicas, las inscripciones a capacitaciones públicas (Cursos) y las peticiones, quejas o reclamos (PQR) frecuentemente se gestionan mediante procesos manuales o software desconectado. Esto provoca tiempos de respuesta lentos, posibles pérdidas de información y poca transparencia para la comunidad.

**La Solución:** Este proyecto proporciona una plataforma unificada y modular que conecta las solicitudes públicas con la administración interna. Ofrece una arquitectura estandarizada donde diferentes áreas de los servicios de bomberos —personal (Bomberos), entidades corporativas (Empresas) y protocolos de seguridad— se integran dentro de un único entorno PHP cohesivo. El resultado es un servicio de bomberos más organizado, transparente y eficiente, capaz de servir a su comunidad con precisión digital.

---

### ✨ Características Principales

El sistema está construido alrededor de una **Arquitectura Modular basada en Controladores**, permitiendo la escalabilidad y administración independiente de funciones específicas del departamento.

#### 🏢 Gestión de Empresas y Entidades (`empresas`)
* **Registros Centralizados:** Mantiene una base de datos sólida de empresas y entidades locales que interactúan con el cuerpo de bomberos.
* **Edición Dinámica:** Permite personalizar perfiles empresariales con información específica requerida para cumplimiento de normas de seguridad y contactos de emergencia.
* **Interacción Basada en Eventos:** Utiliza `manejadorEventos.js` para manejar actualizaciones dinámicas de la interfaz en tiempo real.

#### 🚒 Supervisión de Personal y Bomberos (`bomberos`)
* **Perfiles del Personal:** Gestión integral de registros de bomberos, credenciales y estado interno.
* **Control Modular:** La lógica independiente mediante `claseControladorModulo.php` garantiza que la información del personal esté aislada y segura.
* **Interfaz Administrativa:** Formularios dedicados para crear y editar registros de personal, facilitando actualizaciones para nuevos integrantes o ascensos.

#### 📝 Peticiones, Quejas y Reclamos (`pqr`)
* **Interacción Ciudadana:** Ofrece una vía estructurada para que los ciudadanos envíen peticiones, quejas y solicitudes de recursos.
* **Flujos de Confirmación:** Secuencias automáticas de confirmación mediante `confirmarRegistro.php` garantizan retroalimentación al usuario.
* **Seguimiento de Estados:** Los administradores pueden monitorear el ciclo de vida de cada PQR enviada.

#### 🔍 Inspecciones Técnicas de Seguridad (`inspecciones`)
* **Solicitudes Simplificadas:** Formularios especializados para que las empresas soliciten inspecciones de seguridad contra incendios.
* **Integración de Búsqueda:** `frmBuscarEmpresa.php` permite vincular rápidamente solicitudes con empresas ya registradas.
* **Gestión de Resultados:** Seguimiento de resultados y visitas técnicas para garantizar el cumplimiento de normas de seguridad.

#### 🎓 Administración de Cursos y Capacitaciones (`cursos`)
* **Inscripción Pública:** El módulo `registroInscripciones` permite a los ciudadanos registrarse en capacitaciones de seguridad.
* **Gestión Académica:** Creación y edición de cursos, incluyendo cupos, requisitos y horarios.
* **Mensajería Automatizada:** Plantillas automáticas mediante `mensajeRespuestaInscripcion.php` confirman inmediatamente las inscripciones.

---

### 🛠️ Tecnologías y Arquitectura

El proyecto utiliza una arquitectura clásica compatible con el entorno **LAMP**, enfocándose en modularidad y comportamiento frontend orientado a eventos dentro de un ecosistema PHP.

| Tecnología | Propósito | Razón de Uso |
| :--- | :--- | :--- |
| **PHP** | Lógica Principal Backend | Proporciona un entorno robusto del lado del servidor para manejar formularios complejos y control modular. |
| **JavaScript** | Interactividad Frontend | Utilizado en `manejadorEventos.js` para manejar eventos asíncronos y cambios dinámicos de interfaz. |
| **CSS** | Estilos Visuales | Centralizado en `bomberos-styles.css` para mantener una identidad visual consistente y profesional. |
| **Patrón Modular** | Diseño del Sistema | Garantiza que módulos como “Cursos” no interfieran con “Inspecciones”, facilitando mantenimiento y expansión futura. |

#### Patrón Arquitectónico

Cada módulo dentro del directorio `modulos/` sigue un patrón consistente de **Controlador-Vista-Manejador**:

1. **Lógica:** `claseControladorModulo.php` procesa datos y estados.
2. **Vista:** Los archivos `formulario` y `listado` definen la interfaz.
3. **Manejador:** `manejadorEventos.js` administra interacciones del lado del cliente y llamadas AJAX.

---

### 📁 Estructura del Proyecto

El repositorio está organizado por módulos funcionales, asegurando separación clara entre herramientas administrativas internas y componentes públicos reutilizando utilidades compartidas.

```plaintext
lestebanv-serviciosBomberos/
├── 📄 bomberosServicios.php         # Punto principal de entrada del sistema
├── 📄 Requerimientos pendientes.txt # Documentación de funciones pendientes
├── 📄 leame.txt                     # Notas técnicas y documentación heredada
├── 📄 README.md                     # Guía general del proyecto
├── 📁 includes/                     # Utilidades compartidas del sistema
├── 📁 assets/                       # Recursos estáticos globales
├── 📁 modulos/                      # Módulos funcionales principales
└── 📁 .vscode/                      # Configuración del entorno de desarrollo
```

---

### 🚀 Primeros Pasos

#### Requisitos Previos
- **PHP:** Versión 7.4 o superior recomendada.
- **Servidor Web:** Apache o Nginx.
- **Integraciones:** Diseñado para funcionar como biblioteca o plugin dentro de CMS basados en PHP, como WordPress.

#### Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/lestebanv/serviciosBomberos.git
```

2. **Desplegar en el servidor**
Mueve la carpeta `lestebanv-serviciosBomberos` al directorio raíz de tu servidor o a la carpeta de plugins de tu CMS.

3. **Incluir el núcleo**
```php
require_once('path/to/bomberosServicios.php');
```

4. **Inicializar datos de prueba (Opcional)**
```php
include('includes/insertarDemo.php');
```

---

### 🔧 Uso

#### Vista Administrativa

Los administradores pueden acceder a:

- **Listados:** Ver bomberos, cursos o empresas.
- **Formularios:** Crear nuevos registros usando archivos `formularioCrear...`.
- **Eventos Dinámicos:** El sistema inicializa automáticamente `manejadorEventos.js`.

#### Integración Pública

El sistema proporciona formularios públicos para interacción ciudadana:

- **PQR:** `modulos/publico/registroPqr/formPqr.php`
- **Inscripciones:** `modulos/publico/registroInscripciones/`
- **Shortcodes:** Utiliza las definiciones de `includes/shortcodes.php`.

---

### 🤝 Contribuciones

¡Las contribuciones para mejorar **Servicios Bomberos** son bienvenidas!

### Cómo Contribuir

1. Haz un **Fork** del repositorio.
2. Crea una nueva rama:
```bash
git checkout -b feature/nueva-funcionalidad
```

3. Realiza tus cambios siguiendo el patrón modular existente.
4. Prueba cuidadosamente el sistema.
5. Guarda tus cambios:
```bash
git commit -m 'Add: Nueva validación para inspecciones'
```

6. Sube tu rama:
```bash
git push origin feature/nueva-funcionalidad
```

7. Abre un **Pull Request** describiendo los cambios realizados.

### Guías de Desarrollo

- ✅ Mantener modularidad.
- 📝 Actualizar documentación.
- 🧪 Verificar compatibilidad de eventos JavaScript.
- 🎯 Seguir convenciones camelCase o snake_case.

---

### 📝 Licencia

Este proyecto está bajo la licencia **MIT**.

### Esto significa:

- ✅ Uso comercial permitido.
- ✅ Modificación permitida.
- ✅ Distribución permitida.
- ✅ Uso privado permitido.
- ⚠️ El software se proporciona “tal cual”, sin garantías.

---


  <a href="#">⬆️ Volver Arriba</a>
</p>
