Feature: Generators

  Scenario Outline: Generation
    When I generate a <command> with "<argument>"
    Then I should see "Created:"
    And "<generatedFilePath>" should match my stub

    Examples:
      | command   | argument                  | generatedFilePath                                 |
      | model     | Order                     | app/models/Order.php                              |
      | seed      | orders                    | app/database/seeds/OrdersTableSeeder.php          |
