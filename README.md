# Sarachaga Optimized

Este proyecto es una versión optimizada del sistema de subastas Sarachaga, refactorizado utilizando principios de programación orientada a objetos (OOP). El código está organizado en clases para mejorar la mantenibilidad, seguridad y legibilidad.

## Estructura del Proyecto

```
sarachaga-optimized
├── src
│   ├── Database.php           # Maneja la conexión y consultas a la base de datos
│   ├── AuctionService.php     # Lógica de negocio para subastas
│   ├── BatchService.php       # Lógica de negocio para lotes
│   ├── CategoryService.php    # Lógica de negocio para categorías
│   ├── NightService.php       # Lógica de negocio para noches
│   ├── models
│   │   ├── Auction.php        # Modelo de subasta
│   │   ├── Batch.php          # Modelo de lote
│   │   ├── Category.php       # Modelo de categoría
│   │   └── Night.php          # Modelo de noche
│   └── helpers
│       └── DateHelper.php     # Utilidades para formateo de fechas
│       └── SlugHelper.php     # Utilidades crear Slugs
├── public
│   ├── auctions.php           # Endpoint para obtener datos de subastas
│   └── batch.php              # Endpoint para obtener datos de lotes
│   └── category.php           # Endpoint para obtener datos las categorias con imagenes
├── composer.json              # Configuración de Composer
└── README.md                  # Documentación del proyecto
```

## Instalación

1. **Clona el repositorio:**
   ```sh
   git clone <repository-url>
   cd sarachaga-optimized
   ```

2. **Instala dependencias (opcional):**
   Si usas Composer para dependencias externas:
   ```sh
   composer install
   ```

3. **Configura la base de datos:**
   Edita `src/Database.php` con tus datos de conexión (host, usuario, contraseña, base de datos).

4. **Accede a la aplicación:**
   Usa tu servidor web local (ej: XAMPP) y accede a los endpoints:
   - [http://localhost/sarachaga-optimized/public/auctions.php](http://localhost/sarachaga-optimized/public/auctions.php)
   - [http://localhost/sarachaga-optimized/public/batch.php](http://localhost/sarachaga-optimized/public/batch.php)

## Uso de la API

### Endpoint de Subastas

**GET /public/auctions.php**

Parámetros opcionales:
- `id`: ID de la subasta
- `autor`: Filtrar por autor
- `categoria`: Filtrar por categoría
- `noche`: Filtrar por noche

**Ejemplo de respuesta:**
```json
{
  "subasta": {
    "id": 1,
    "nro": 123,
    "fechainicio": { "system": "2024-07-21", "format": "Domingo, 21 de julio de 2024" },
    ...
  },
  "noches": [
    {
      "idSubasta": 1,
      "noche": 1,
      "dia": "Sábado, 20 de julio de 2024",
      "horario": { "system": "20.00", "format": "20:00" },
      ...
    }
  ],
  "categorias": [
    { "id": 1, "nombre": "Pintura" }
  ],
  "autores": [
    "Anónimo",
    "Juan Pérez"
  ],
  "lotes": [
    { "id": 10, "titulo": "Obra 1", ... }
  ]
}
```

### Endpoint de Lotes

**GET /public/batch.php?id=10**

**Ejemplo de respuesta:**
```json
{
  "lote": { "id": 10, "titulo": "Obra 1", ... },
  "subasta": { ... },
  "noche": { ... },
  "categoria": { ... }
}
```

## Buenas Prácticas

- Los modelos representan entidades de la base de datos y no contienen lógica de presentación.
- Los servicios encapsulan la lógica de negocio y acceso a datos.
- El formateo de fechas y otros datos de presentación se realiza en los servicios o controladores, no en los modelos.
- Todas las consultas SQL usan sentencias preparadas para evitar inyección SQL.
## Contribuciones

Las contribuciones externas no están permitidas, ya que este proyecto es un desarrollo personalizado para uso interno de la empresa.

## Licencia

Este proyecto es software privado y su uso, copia o distribución está restringido exclusivamente a la empresa propietaria. Para más información, contacta al responsable del proyecto.