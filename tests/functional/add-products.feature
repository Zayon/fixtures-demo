@product @add-products
Feature:
  In order to offer a large choice of products
  As a product seller
  I want to add products

  Background:
    Given the fixtures file "add-products.yaml" is loaded

  Scenario: Add a product
    Given I send a "GET" request to "/products/"
    And the response status code should be 200
    And the JSON node "total" should be equal to the number 5
    When I send a "POST" request to "/products/" with body:
    """
    {
      "name": "Super cool drill",
      "price": 249.99,
      "category": {{ category_tools.id }}
    }
    """
    Then the response status code should be 200
    And I send a "GET" request to "/products/"
    And the JSON node "total" should be equal to the number 6
