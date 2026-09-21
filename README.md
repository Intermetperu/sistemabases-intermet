# Sistema de Base de Contactos - Intermet

Sistema de gestión de contactos (CRM) desarrollado en CodeIgniter 4 para Intermet. Permite administrar, segmentar y comunicarse con una base de contactos a gran escala, con carga masiva, detección de duplicados y envío de correos.

## Características

-  **Gestión de contactos**: base de datos centralizada con miles de registros (nombre, empresa, cargo, país, correo, LinkedIn, etc.)
-  **Carga masiva vía CSV/Excel**: importación de contactos con separación por `;` o `,`
-  **Asociación por evento**: etiqueta contactos nuevos o actualizados según el evento del que provienen
-  **Búsqueda y filtros avanzados**: por nombre, empresa, correo, cargo, país, LinkedIn, evento
-  **Detección de duplicados**: identifica y gestiona contactos repetidos
-  **Segmentación y exportación**: exporta listas filtradas a CSV/Excel
-  **Envío de correos masivos**: campañas de correo a listas segmentadas, con historial y logs de envío
-  **Historial y auditoría**: registro de cargas, envíos y cambios
-  **Panel administrativo**: gestión de usuarios, roles y permisos
-  **Dashboard**: panel de control con métricas generales

## Tecnologías

- **Framework**: CodeIgniter 4 (PHP)
- **Base de datos**: MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript
- **Servidor local**: Laragon

## Estructura del proyecto


## Requisitos

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Extensión `intl`, `mbstring` habilitadas

## Instalación

```bash
git clone https://github.com/Intermetperu/sistemabases-intermet.git
cd sistemabases-intermet
composer install
cp env .env
```

Configura tu archivo `.env` con los datos de tu base de datos y credenciales de correo:
