# Reporteador ECC - AI Powered Reporting System 🤖

> **Sistema de Generación de Informes de Seguridad Asistido por Inteligencia Artificial.**
> *Automatización de redacción, generación de evidencia PDF y distribución vía SMTP para entornos corporativos.*

---

## 🎯 ¿Qué es y qué resuelve?

En la operación de seguridad privada, los supervisores pierden horas redactando informes de incidencias ("Novedades"), a menudo con errores de ortografía o falta de coherencia.

**Reporteador ECC** soluciona esto integrando la API de **Google Gemini Pro**. El oficial simplemente ingresa palabras clave o una descripción coloquial de los hechos, y la IA reescribe el texto con un tono formal, ejecutivo y técnico, listo para ser presentado al cliente.

**Capacidades Clave:**
* ✨ **Reescritura con IA:** Transforma "el guardia se durmió" en "Se detectó al elemento en postura no alerta durante su ronda...".
* 📄 **Generación PDF:** Crea documentos legales con encabezados, fechas y evidencia fotográfica incrustada (FPDF).
* 📧 **Distribución Automática:** Envía el reporte final a la lista de distribución de interesados vía SMTP.

---

## 📸 Flujo de Trabajo (Screenshots)

### 1. Panel de Captura Inteligente
Interfaz limpia donde el monitorista carga los datos básicos y la evidencia.
![Dashboard Principal](PON_AQUI_URL_DE_TU_DASHBOARD)

### 2. Procesamiento de IA
El sistema consulta a la API de Gemini para estructurar la narrativa del incidente antes de generar el documento.
![Procesamiento](PON_AQUI_URL_DE_OTRA_VISTA_SI_TIENES)

### 3. Output Final (PDF)
El resultado es un archivo PDF estandarizado que se envía automáticamente por correo.
![PDF Generado](PON_AQUI_URL_DEL_PDF_O_CORREO)

---

## 🛠️ Stack Tecnológico

* **Backend:** PHP 8.0+ (Nativo)
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **AI Engine:** Google Gemini Pro API
* **Base de Datos:** MySQL
* **Librerías:** FPDF (PDF) y PHPMailer (SMTP)

---

## 👨‍💻 Guía de Despliegue para Desarrolladores

Si deseas implementar o probar este sistema en tu propio servidor local o hosting, sigue estos pasos.

### ⚠️ Notas Importantes (Archivos no incluidos)
Por razones de seguridad y optimización, este repositorio **NO** incluye:
1.  La carpeta `libs/` (Debes descargar FPDF y PHPMailer manualmente).
2.  La carpeta `uploads/` (Debes crearla manualmente).
3.  Credenciales reales (Debes configurar tus propias llaves).

### 🚀 Pasos de Instalación

#### 1. Clonar y Estructurar
Descarga el repositorio y crea las carpetas faltantes en la raíz de tu proyecto:
```bash
mkdir uploads
mkdir libs
