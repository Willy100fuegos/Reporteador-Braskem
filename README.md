# 📑 Reporteador de Novedades - Braskem Idesa System

Este repositorio presenta el **Ecosistema Digital de Reporteo** diseñado para la supervisión de seguridad privada. El sistema integra tecnologías de backend para la captura, procesamiento y análisis de incidentes operativos.

## 🚀 Funcionalidades Core
* **IA Text Enhancement:** Uso de la API de Gemini para la corrección gramatical y técnica de reportes.
* **Automatización PDF:** Generación dinámica de reportes bajo estándar corporativo usando FPDF.
* **Dashboard C4 (V2):** Visualización analítica de incidentes basada en PHP y Chart.js.
* **Gestión de Sesiones:** Control de acceso restringido para personal administrativo del C4.

## 🏗️ Arquitectura del Sistema
El sistema completo reside en un entorno de producción seguro y utiliza:
1. **Frontend:** PHP/Tailwind CSS con persistencia de borradores mediante LocalStorage.
2. **Backend:** PHP 8.x con integración de librerías `fpdf` y `PHPMailer`.
3. **Database:** MySQL para el almacenamiento estructurado de folios y logs.

---
⚠️ **Nota de Seguridad:** Por razones de confidencialidad y protección de datos, el código fuente que contiene credenciales críticas (API Keys, SMTP Passwords y DB Strings) se mantiene en un entorno privado. Este repositorio sirve únicamente como vitrina de arquitectura y despliegue estático.
