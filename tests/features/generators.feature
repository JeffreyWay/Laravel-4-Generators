Feature: Generators

  Scenario Outline: Generation
    When I generate a <command> with "<argument>"
    Then I should see "Created:"
    And "<generatedFilePath>" should match my stub

    Examples:
      | command   | argument                  | generatedFilePath                                 |
      | model     | Order                     | app/models/Order.php                              |
      | seed      | orders                    | app/database/seeds/OrdersTableSeeder.php          |
      | migration | create_orders_table       | app/database/migrations/CreateOrdersTable.php     |
      | migration | delete_orders_table       | app/database/migrations/DeleteOrdersTable.php     |
      | migration | add_title_to_orders_table | app/database/migrations/AddTitleToOrdersTable.php |
