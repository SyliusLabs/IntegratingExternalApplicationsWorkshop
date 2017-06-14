Integrating external applications with Sylius
=============================================

Installation
------------

Clone this repository by running the following command:

```sh
$ git clone https://github.com/SyliusLabs/IntegratingExternalApplicationsWorkshop.git
```

Install the dependencies by running `composer install` in both `RecommendationEngine` and `Sylius` directories.

We recommend to load these directories as different PhpStorm projects in order to receive the best autocompletion support.

Timeline
--------

1. **Sylius**: dispatching an AMQP message based on orders’ post complete ResourceControllerEvent.

   Post complete event is named `sylius.order.post_complete`.

   We will use [OldSound RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle) to handle these messages.

   AMQP message contract:
   
   ```json
   {
       "type": "customer_placed_order",
       "payload": {
           "customerEmail": "customer@example.com",
           "orderToken": "aa61ce8bb0efeef6153fccc047df4550",
           "productsCodes": ["tv", "router", "computer"]
       }
   }
   ```
   
   You can test if your implementation is working correctly by running the PHPUnit integration test:
   
   ```bash
   $ bin/phpunit tests/RecommendationEngineIntegrationTest.php
   ```

2. **RecommendationEngine**: denormalise the AMQP message to an event.

   We will use [SyliusLabs/RabbitMqSimpleBusBundle](https://github.com/SyliusLabs/RabbitMqSimpleBusBundle)
   to denormalise the message and publish it on the event bus.

3. **RecommendationEngine**: handle the event and update Neo4j database (projector) based on provided models.

   In order to use Neo4j you need to change the `neo4j` user password by logging in at `http://localhost:7474/browser/`
   with `neo4j` username and `neo4j` password.

   We will use [GraphAware's Neo4j OGM](https://github.com/graphaware/neo4j-php-ogm) to persist models to Neo4j 
   and [SimpleBus' EventBus](http://simplebus.github.io/SymfonyBridge/doc/event_bus_bundle.html) to handle the event dispatched before.

4. **RecommendationEngine**: write an API endpoint for getting product recommendations.

   This API endpoint should be bound to `/recommented-products/{productCode}` and return an array of 
   recommended products' codes in JSON format like the following:
   
   ```
   GET /recommented-products/product_1
   
   ["recommended_product_1", "recommended_product_2", "recommended_product_3"]
   ```
   
   If `limit` GET parameter is passed, it should restrict this endpoint to return maximum `limit` recommended products:
   
   ```
   GET /recommented-products/product_1?limit=1
   
   ["recommended_product_1"]
   ```

   Repository method providing recommended products' codes based on product code is already provided.
    
   You can test if your implementation is working correctly by running the PHPUnit integration test:
      
   ```bash
   $ bin/phpunit tests/RecommendationEngineIntegrationTest.php
   ```

5. **Sylius**: write a recommendation provider which returns the recommended products.

   The proposed architecture involves `ProductRecommendationProvider` which returns an array of
   recommended products entities. It would use `ProductRecommendationClient` which returns an array of
   recommended products codes based on the response of recommendation engine API endpoint. This will
   make caching of these products codes easier and achievable by decorator pattern.

6. **Sylius**: write a partial rendering the recommended products.
