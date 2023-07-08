# Challenges - TM_BestOfferSeller

## Setup Environment
El enviroment se para este challenge es de nuestro querido amigo [MarkShust markshust/docker-magento](https://github.com/markshust/docker-magento). 
Puede utilizar cualquier ambiente que le parezca comodo para realizar las pruebas. 

Tengo un pequeño scripts que ayudara a levantar el ambiente rapidamente con la ayuda de markshust/docker-magento.

Siga el siguiente paso.

```sh
curl -o- https://raw.githubusercontent.com/mmaximo33/challenge_tm/main/bin/deploy_lcl.sh | bash

wget -qO- https://raw.githubusercontent.com/mmaximo33/challenge_tm/main/bin/deploy_lcl.sh | bash
```

**Requerimientos** 

- Linux
- Docker
- Curl

**Que ocurrira?**

- Se creara un directorio en ~/Domains/project
- Se apagagara apache
- Se apagaran todos los contenedores que tenga corriendo
- Se descargara el proyecto
  - Importara base de datos
  - Aplicaran configuraciones
  - Se levantara el proyecto 100% funcional

## Index
- [Challenge](#challenge)
  - [Contexto](#contexto)
  - [Contexto Tecnico](#contexto-técnico)
- [Analisis de solucion](#analisis-de-solucion)
- [Seller/Provider](#sellerprovider)
- [Marketplace](#marketplace)
  - [Resumen](#resumen)
  - [Requerimientos implicitos](#requerimiento-implicitos)
  - [Oportunidad de mejora](#oportunidad-de-mejora)
    - [Stock](#stock)
    - [Quote](#quote)
    - [Performance](#performance)

# Challenge
## Contexto
Se ha establecido un acuerdo con un nuevo proveedor al cual es necesario integrar. Dicho proveedor posee varios
depósitos distribuidos en diferentes localidades, lo cual no debería afectar a la experiencia de compra del cliente.
Con el fin de garantizar esto, se requiere desarrollar un sistema que permita integrarse con el proveedor y definir
cuál es la mejor opción para nuestros clientes en función de la información que este nos proporcione.

Es importante destacar que el proveedor tiene otros clientes por lo cual el stock puede variar por oferta. Siempre
que haya una oferta disponible debe mostrarse por lo cual por ejemplo: Si el sistema determina una oferta como la
mejor pero la misma no tiene stock debe retornarse siempre que se pueda la siguiente mejor. De esta manera
siempre se mostraria un oferta con stock.

## Contexto Técnico
El proveedor dispone de la siguiente API:
Obtener todas las ofertas para un sku específico.

### Obtener todas las ofertas para un sku específico.
Request Example: 
```sh
curl --location --request GET '{provider_host}/getAllSkuOffers/:sku'
```
Response Status:
```sh
200 OK Successful response
```

Successful Response Body:
```sh
{
   "sku":"xxx", //string
   "offers":[
      {
         "id":0, // integer
         "price":00.00, // decimal
         "stock":0, // integer - Cantidad de ofertas disponible
        "shipping_price":0.00, // decimal
         "delivery_date":2023-05-27, // date
         "can_be_refunded":true, // boolean - Determina si un producto devolucion
         "status":"new", // string (new,used,renew)
         "renew)""guarantee":true, // boolean - Determina si un producto tiene
         "seller":{
            "name":"xxxx",  // string
            "qualification": 0, // integer - Range: 0-5 - Promedio de calificaciones.
            "reviews_quantity": 0, // integer - Cantidad de calificaciones que tiene el seller.
         }
      },
      "..."
   ]
}
```

# Analisis de solucion

Inicialmente tengemos dos elementos, por un lado un seller y por otro el marketplace que concenctra sellers.

## Seller/Provider
### Module: Tm_Provider <a href="./app/code/Tm/Provider" target="_blank">See Doc</a>

Este seller debe exponer endpoints para consultar 
- Informacion relevante de su uso
- Visualizar todos los datos de ejemplo configurados.
- Visualizar todas las ofertas para un sku particular (Este ultimo es el que usaremos definitivamente)

## Marketplace
### Module: Tm_BestOfferSeller <a href="./app/code/Tm/BestOfferSeller" target="_blank">See Doc</a>

Este modulo contendra toda la logica pesada que se realiza en una tienda de Magento 2 segun los requerimientos mencioandos en al comienzo.

### Resumen

En resumidas cuentas se debe consultar el endpoint disponible de un seller segun el SKU y en caso que este devuelva seleccionar la mejor segun las condiciones establecidas.

### Requerimiento implicitos

1. El endpoint del seller puede llegar a falla, debe gestionarse esta condicion.
2. Se debe definir el calculo para determinar la mejor oferta devuelta por el seller. 
   1. Solo si la oferta es menor que el precio del producto se debe mostrar al customer en el frontend
3. El nuevo precio debe ser visualizado en PDP, PDL, PDP
   1. En estas page se debe indicar si un precio es por oferta o no
4. El precio debe ser trasladado y visualizado en el frontend en los siguientes procesos. 
   1. Add to cart (quote)
   2. Checkout process (sales)
   3. En cada uno de los procesos anteriores se debe guardar la mejor oferta del seller para su trazabilidad.
5. Es necesario endpoint para comprobar rapidamente que oferta es mejor por sku 
6. Es necesario endpoint para obtener ordenes con BestOfferSeller por SKU
7. Es necesario endpoint para obtener ordenes con BestOfferSeller por Date
8. Es necesario endpoint para descargar archivo {date}_bestofferseller.csv por date para sus analisis.
9. CommandLine, debe estar disponible el comando para generar el reporte de ordenes con oferta por dia. Este CLI sera ejecutado con un crontab
10. Considerar translations
11. Para mejorar en cuestion de perfomance se debe crear una tabla con su model, resourcemodel y collection para ser consultada rapidamente.


### Oportunidad de mejora

### Stock

El calculo de la oferta se realiza en funcion del precio, actualmente no se contempla el stock de la mejor oferta. 
Esto supone un problema en caso de que el **customer** desee agregar al **cart** mas productos de lo que posee la oferta. 

___Analisis/requerimiento:___
- En caso de que la cantidad de producto solicitada por el **customer** del marketplace, supere la cantidad disponible en la oferta se debe. 
  - Mostrar al usuario la cantidad disponible para esa oferta.
  - En caso que desee continuar agregando, se debe contemplar la siguiente mejor oferta
    - En caso que no exista siguiente mejor oferta, se tomara el valor del producto. 

### Quote

Actualmente solo se gestiona el quote interno y de forma basica. 
Esto puede suponer grandes problemas teniendo en cuenta que es complicado cuando se depende de servicios de terceros, ya se a nivel performance o modificaciones de datos, donde no se puede determinar la frecuencia de estas.

**Stock**

Es importante tener presente que no se puede escular que el customer dentro del marketplace terminara la compra instantaneamente luego de agregar el producto al **cart**, pueden transcurrir varios minutos o inclusive dias. 
Esto puede ser un problema si el Seller tiene una **alta rotación** de ese productos. 

___Analisis/Requerimiento.___ 

- Al momento de agregar un producto desde el marketplace al cart, debe enviarse al seller esta reserva. 
- Al momento de sacar el producto del cart debe enviarse al seller esta cancelacion de la reserva para liberar stock.
- En caso que el seller cancele la reserva en su sistema, debe notificar al Marketplace la cancelacion.
  - Debe existir un endpoint el cual reciba quote quote_item para su cancelacion.
- Seguro de stock: El seller debe configurar el stock que tendra disponible para el marketplace, a fin de evitar agotar todas las unidades.
  - Esto quiere decir que si posee 100 unidades, solo debera dejar a disposicion en la oferta un porcentaje menor a %70. 

### Performance

Consultar las ofertas del seller cada vez que se trae un producto puede representar un alto costo a nivel performance, ya que como se menciono antes, depender de servicios de terceros puede ser un poco complicado. 

___Analisis/Requerimiento.___

Para resolver este problema se podria
- Crear un servicio independiente en otra instancia
- Crear una tabla auxiliar tm_bestofferseller_currents_offers
- Consultar por medio de crontab todas las ofertas y almacenarlas aqui.
  - Es importante que tenga una frencuencia razonable [5m 10m] 

En el marketplace
Al momento de consultar en el Marketplace un product, buscar las disponibles oferta en este nuevo servicio.
- Si existe oferta la carga para el customer.
  - Continua su flujo normal hasta el proceos de checkout
  - En el proceso de checkout se toma el id de la oferta y se consulta directamente con el seller para consolidar esta transaccion.
    - La tasa de customer que llegar al proceso de checkout, es mucho mas baja que aquellos que consultan un producto.
    - En caso que la oferta no exista o se modifique se debe mostrar esto al usuario final.

