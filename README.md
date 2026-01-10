# 📊 Reporteador Digital C4 - Braskem Idesa (AI Powered)

> **Sistema de Generación de Informes de Seguridad Asistido por Inteligencia Artificial.**
> *Transformación digital del reporte manual de novedades hacia un proceso automatizado con inteligencia aplicada y dashboards de control ejecutivo.*

<p align="center">
  <img src="https://pixmedia.b-cdn.net/pixmedialogoblanco.png" width="208" height="33" alt="Pixmedia Agency">
</p>

---

## 1. 🖼️ Evidencia del Sistema (Showcase)

Este ecosistema integra interfaces de captura inteligente, tableros de control y generación de entregables legales.

| **Interfaz de Captura (AI)** | **Dashboard de Inteligencia** | **Entregable PDF** |
|:---:|:---:|:---:|
| <img src="http://imgfz.com/i/OxbfPJ3.png" width="300"> | <img src="http://imgfz.com/i/RzEUfec.png" width="300"> | <img src="http://imgfz.com/i/LtpKVbF.png" width="300"> |
| *Formulario con motor de IA para corrección de redacción.* | *Panel de control para análisis de KPIs.* | *Reporte generado y enviado por SMTP.* |

---

## 2. 🚀 Características Técnicas Principales

* **Optimización de Textos con IA:** Integración de la API de **Google Gemini Pro** para la refinación gramatical y técnica de novedades operativas en tiempo real.
* **Generador de Reportes Automático:** Motor de backend basado en PHP que procesa los datos para generar documentos PDF estandarizados con foliado de seguridad.
* **Dashboard C4 Interactivo:** Panel centralizado que permite a los monitoristas filtrar, buscar y visualizar tendencias de incidentes mediante gráficas dinámicas (Chart.js).
* **Gestión de Sesiones Seguras:** Control de acceso mediante login administrativo para restringir la visualización de datos sensibles.

---

## 3. 🛠️ Arquitectura Tecnológica

El sistema reside en una arquitectura LAMP optimizada:

* **Backend:** PHP 8.x (Nativo) para lógica de negocio y procesamiento.
* **Frontend:** HTML5, Tailwind CSS y JavaScript (Fetch API) con persistencia de borradores en LocalStorage.
* **Database:** MySQL para el almacenamiento estructurado de folios y logs.
* **Librerías:** `FPDF` para renderizado de documentos y `PHPMailer` para distribución SMTP.
* **AI Core:** Proxy intermedio para comunicación segura con LLMs.

---

## 4. 👨‍💻 Guía de Despliegue para Desarrolladores

Si eres desarrollador y deseas probar o contribuir a este proyecto, ten en cuenta que el código ha sido **sanitizado** por seguridad.

### ⚠️ Requisitos Previos (Archivos Excluidos)
Para mantener el repositorio ligero y seguro, **NO** se incluyen las siguientes carpetas. Debes crearlas manualmente:

1.  **Carpeta `uploads/`**: Crea esta carpeta en la raíz para que se guarden las imágenes de evidencia y los PDFs generados.
2.  **Carpeta `libs/`**: Debes descargar las dependencias y colocarlas aquí:
    * [Descargar FPDF](http://www.fpdf.org/) -> Descomprimir en `libs/fpdf/`
    * [Descargar PHPMailer](https://github.com/PHPMailer/PHPMailer) -> Descomprimir en `libs/phpmailer/`

### ⚙️ Configuración de Entorno
Debes editar los siguientes archivos con tus propias credenciales para que el sistema funcione:

* **`config.php`**: Ingresa tus credenciales de MySQL (`DB_HOST`, `DB_USER`, `DB_PASS`).
* **`ia_proxy.php`**: Reemplaza `TU_API_KEY_DE_GEMINI` con tu propia llave de Google AI Studio.
* **`procesar.php`**: Configura las credenciales de tu servidor SMTP para el envío de correos.

---

## 👨‍💻 Sobre el Desarrollador
**William Velázquez Valenzuela**
* **Cargo:** Director de Tecnologías | Administrador de Sistemas
* **Ubicación:** Coatzacoalcos, Veracruz
* **Agencia:** Pixmedia Agency
