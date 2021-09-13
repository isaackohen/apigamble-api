
  var scores, roundScores, activePlayer, gamePlaying, lastDice;

  // call intialised function
  init();
  // on click of btn roll
  document.querySelector('.btn-roll').addEventListener('click', function() {
    // if game playing is true
    if (gamePlaying) {

      // 1. random number
      var dice = Math.floor(Math.random() * 6 + 1);
      var dice2 = Math.floor(Math.random() * 6 + 1);

      //2. Display the dice img based on random number
      var diceDOM = document.querySelector('.dice');
      var diceDOM2 = document.querySelector('.dice2');

      diceDOM.style.display = 'block';
      diceDOM2.style.display = 'block';

      diceDOM.src = 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/243004/dice-' + dice + '.png';
      diceDOM2.src = 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/243004/dice-' + dice2 + '.png';

      //3. Update the round score IF the rolled number was NOT a 1
      if (dice !== 1 && dice2 !== 1) {
        // get the round score from dice rolls
        roundScore += dice + dice2;
        // update the current players score with round score, based on active player
        document.querySelector('#current-' + activePlayer).textContent = roundScore;
        // check if last dice roll is equal to current dice roll
      } else {
        // call next player function
        nextPlayer();
      }
      // set last dice roll to dice roll
      lastDice = dice;
    }

  });
  // hold button on click
  document.querySelector('.btn-hold').addEventListener('click', function() {
    // only if game playing is true
    if (gamePlaying) {
      // Add CURRENT score to GLOBAL score
      scores[activePlayer] += roundScore;

      // Update the active player score 
      document.querySelector('#score-' + activePlayer).textContent = scores[activePlayer];

      // get winning score
      var input = document.querySelector('.winningScore').value;
       var winningScore;
      // if there is an input
      if(input) {
         winningScore = input;
      } else {
        // without input default score
        winningScore = 100;
      }

      // Check if player won the game based on target score
      if (scores[activePlayer] >= winningScore) {
        // add text winner to active player
        document.querySelector('#name-' + activePlayer).textContent = 'Winner!';
        // hide dice 1
        document.querySelector('.dice').style.display = 'none';
        // hide dice 2
        document.querySelector('.dice2').style.display = 'none';
        // adding winner class to active player panel
        document.querySelector('.player-' + activePlayer + '-panel').classList.add('winner');
        // remove the active class from active player panel
        document.querySelector('.player-' + activePlayer + '-panel').classList.remove('active');
        gamePlaying = false;
      } else {
        //next player
        nextPlayer();
      }
    }

  });

  function nextPlayer() {
    // player change 
    // if active player = 0, change to player 1, else active player = 0
    activePlayer === 0 ? activePlayer = 1 : activePlayer = 0;
    // reset 
    roundScore = 0;

    // reset currents to 0 
    document.getElementById('current-0').textContent = '0';
    document.getElementById('current-1').textContent = '0';
    // toggle active on panels
    document.querySelector('.player-0-panel').classList.toggle('active');
    document.querySelector('.player-1-panel').classList.toggle('active');

  }

  document.querySelector('.btn-new').addEventListener('click', init);

  function init() {
    document.querySelector('.winningScore').textContent = '';

    scores = [0, 0];
    roundScore = 0;
    activePlayer = 0;
    gamePlaying = true;

    // hide the dice
    document.querySelector('.dice').style.display = 'none';
    document.querySelector('.dice2').style.display = 'none';

    // reset all scores and current
    document.getElementById('score-0').textContent = '0';
    document.getElementById('score-1').textContent = '0';
    document.getElementById('current-0').textContent = '0';
    document.getElementById('current-1').textContent = '0';
    // change player names back
    document.getElementById('name-0').textContent = 'Player 1';
    document.getElementById('name-1').textContent = 'Player 2';

    // remove winner class to active player panel
    document.querySelector('.player-0-panel').classList.remove('winner');
    document.querySelector('.player-1-panel').classList.remove('winner');

    // remove active class to active player panel
    document.querySelector('.player-0-panel').classList.remove('active');
    document.querySelector('.player-1-panel').classList.remove('active');
    document.querySelector('.player-0-panel').classList.add('active');

  }

  // two 6 in a row player loses score and goes to next player