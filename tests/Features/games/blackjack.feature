@functional @blackjack
Feature:
  As a player
  I should be able to play a game of Blackjack through the Symfony2 CLI

  # Adding cards in the deck works the following way:
  # | rank |
  # |    5 | #first two dealer cards
  # |    5 |

  # |    5 | #first two player cards
  # |    5 |

  # |    3 | #following cards
  # |    7 |

  Background:
    Given I trick the deck
    And I register the Blackjack command

  Scenario: Dealer wins by outscoring player
    Given the deck returns the following cards in a FILO order:
      |  6 |
      |  6 |

      |  5 |
      |  5 |

      |  5 |

    And I call "stand" when asked for my move
    When I run the blackjack game command
    Then the player score should be "10"
    And the dealer score should be "17"
    And the dealer should have won

  Scenario: Player beats the dealer with the right hits
    Given the deck returns the following cards in a FILO order:
      | 10 |
      |  9 |

      | 10 |
      |  2 |

      |  6 |
      |  2 |
    And I call "hit, hit, stand" when asked for my move
    When I run the blackjack game command
    Then the player score should be "20"
    And the dealer score should be "19"
    And the player should have won
    
  Scenario: Dealer beats player with a Blackjack
    Given the deck returns the following cards in a FILO order:
      | 10 |
      |  1 |

      | 10 |
      |  2 |
    And I neither will have to hit nor stand
    When I run the blackjack game command
    Then the dealer should have won
    And I should see "Blackjack!" in the command output

  Scenario: Player beats the dealer with a Blackjack
    Given the deck returns the following cards in a FILO order:
      | 10 |
      |  5 |

      | 10 |
      |  1 |
    And I neither will have to hit nor stand
    When I run the blackjack game command
    Then the player should have won
    And I should see "Blackjack!" in the command output

  Scenario: A draw with two both dealer and player having a Blackjack
    Given the deck returns the following cards in a FILO order:
      | 1  |
      | 10 |

      | 10 |
      |  1 |
    And I neither will have to hit nor stand
    When I run the blackjack game command
    Then I should see "Draw!" in the command output

  Scenario: I can rematch
    Given the deck returns the following cards in a FILO order:
      | 10 |
      |  5 |

      | 10 |
      |  1 |
    And I neither will have to hit nor stand
    And I should be asked for a rematch and answer "y"
    Then I run the blackjack game command

  Scenario: I can stop playing
    Given the deck returns the following cards in a FILO order:
      | 10 |
      |  5 |

      | 10 |
      |  1 |
    And I neither will have to hit nor stand
    And I should be asked for a rematch and answer "n"
    Then I run the blackjack game command
    And I should see "Thank you for playing!" in the command output
