@product @list-products
Feature:
  In order to select products
  As a customer
  I want to see the products available

  Background:
    Given the fixtures file "list-products.yaml" is loaded

  Scenario: List all products
    When I send a "GET" request to "/products/"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "total" should be equal to the number 15

  Scenario: List products of a given category
    When I send a "GET" request to "/products/" with parameters:
      | key      | value |
      | category | tools |
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "total" should be equal to the number 10
